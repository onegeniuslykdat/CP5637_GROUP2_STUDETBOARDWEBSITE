<?php

namespace Staatic\Vendor\phpseclib3\Crypt;

use ReflectionClass;
use RuntimeException;
use InvalidArgumentException;
use LengthException;
use Staatic\Vendor\phpseclib3\Crypt\Common\AsymmetricKey;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Montgomery as MontgomeryCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards as TwistedEdwardsCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Curve25519;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Ed25519;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Ed448;
use Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Keys\PKCS1;
use Staatic\Vendor\phpseclib3\Crypt\EC\Parameters;
use Staatic\Vendor\phpseclib3\Crypt\EC\PrivateKey;
use Staatic\Vendor\phpseclib3\Crypt\EC\PublicKey;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedAlgorithmException;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedCurveException;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedOperationException;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\ECParameters;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class EC extends AsymmetricKey
{
    const ALGORITHM = 'EC';
    protected $QA;
    protected $curve;
    protected $format;
    protected $shortFormat;
    private $curveName;
    protected $q;
    protected $x;
    protected $context;
    protected $sigFormat;
    public static function createKey($curve)
    {
        self::initialize_static_variables();
        $class = new ReflectionClass(static::class);
        if ($class->isFinal()) {
            throw new RuntimeException('createKey() should not be called from final classes (' . static::class . ')');
        }
        if (!isset(self::$engines['PHP'])) {
            self::useBestEngine();
        }
        $curve = strtolower($curve);
        if (self::$engines['libsodium'] && $curve == 'ed25519' && function_exists('sodium_crypto_sign_keypair')) {
            $kp = sodium_crypto_sign_keypair();
            $privatekey = EC::loadFormat('libsodium', sodium_crypto_sign_secretkey($kp));
            $privatekey->curveName = 'Ed25519';
            return $privatekey;
        }
        $privatekey = new PrivateKey();
        $curveName = $curve;
        if (preg_match('#(?:^curve|^ed)\d+$#', $curveName)) {
            $curveName = ucfirst($curveName);
        } elseif (substr($curveName, 0, 10) == 'brainpoolp') {
            $curveName = 'brainpoolP' . substr($curveName, 10);
        }
        $curve = 'Staatic\Vendor\phpseclib3\Crypt\EC\Curves\\' . $curveName;
        if (!class_exists($curve)) {
            throw new UnsupportedCurveException('Named Curve of ' . $curveName . ' is not supported');
        }
        $reflect = new ReflectionClass($curve);
        $curveName = $reflect->isFinal() ? $reflect->getParentClass()->getShortName() : $reflect->getShortName();
        $curve = new $curve();
        if ($curve instanceof TwistedEdwardsCurve) {
            $arr = $curve->extractSecret(Random::string(($curve instanceof Ed448) ? 57 : 32));
            $privatekey->dA = $dA = $arr['dA'];
            $privatekey->secret = $arr['secret'];
        } else {
            $privatekey->dA = $dA = $curve->createRandomMultiplier();
        }
        if ($curve instanceof Curve25519 && self::$engines['libsodium']) {
            $QA = sodium_crypto_box_publickey_from_secretkey($dA->toBytes());
            $privatekey->QA = [$curve->convertInteger(new BigInteger(strrev($QA), 256))];
        } else {
            $privatekey->QA = $curve->multiplyPoint($curve->getBasePoint(), $dA);
        }
        $privatekey->curve = $curve;
        $privatekey->curveName = $curveName;
        if ($privatekey->curve instanceof TwistedEdwardsCurve) {
            return $privatekey->withHash($curve::HASH);
        }
        return $privatekey;
    }
    /**
     * @param mixed[] $components
     */
    protected static function onLoad($components)
    {
        if (!isset(self::$engines['PHP'])) {
            self::useBestEngine();
        }
        if (!isset($components['dA']) && !isset($components['QA'])) {
            $new = new Parameters();
            $new->curve = $components['curve'];
            return $new;
        }
        $new = isset($components['dA']) ? new PrivateKey() : new PublicKey();
        $new->curve = $components['curve'];
        $new->QA = $components['QA'];
        if (isset($components['dA'])) {
            $new->dA = $components['dA'];
            $new->secret = $components['secret'];
        }
        if ($new->curve instanceof TwistedEdwardsCurve) {
            return $new->withHash($components['curve']::HASH);
        }
        return $new;
    }
    protected function __construct()
    {
        $this->sigFormat = self::validatePlugin('Signature', 'ASN1');
        $this->shortFormat = 'ASN1';
        parent::__construct();
    }
    public function getCurve()
    {
        if ($this->curveName) {
            return $this->curveName;
        }
        if ($this->curve instanceof MontgomeryCurve) {
            $this->curveName = ($this->curve instanceof Curve25519) ? 'Curve25519' : 'Curve448';
            return $this->curveName;
        }
        if ($this->curve instanceof TwistedEdwardsCurve) {
            $this->curveName = ($this->curve instanceof Ed25519) ? 'Ed25519' : 'Ed448';
            return $this->curveName;
        }
        $params = $this->getParameters()->toString('PKCS8', ['namedCurve' => \true]);
        $decoded = ASN1::extractBER($params);
        $decoded = ASN1::decodeBER($decoded);
        $decoded = ASN1::asn1map($decoded[0], ECParameters::MAP);
        if (isset($decoded['namedCurve'])) {
            $this->curveName = $decoded['namedCurve'];
            return $decoded['namedCurve'];
        }
        if (!$namedCurves) {
            PKCS1::useSpecifiedCurve();
        }
        return $decoded;
    }
    public function getLength()
    {
        return $this->curve->getLength();
    }
    public function getEngine()
    {
        if (!isset(self::$engines['PHP'])) {
            self::useBestEngine();
        }
        if ($this->curve instanceof TwistedEdwardsCurve) {
            return ($this->curve instanceof Ed25519 && self::$engines['libsodium'] && !isset($this->context)) ? 'libsodium' : 'PHP';
        }
        return (self::$engines['OpenSSL'] && in_array($this->hash->getHash(), openssl_get_md_methods())) ? 'OpenSSL' : 'PHP';
    }
    public function getEncodedCoordinates()
    {
        if ($this->curve instanceof MontgomeryCurve) {
            return strrev($this->QA[0]->toBytes(\true));
        }
        if ($this->curve instanceof TwistedEdwardsCurve) {
            return $this->curve->encodePoint($this->QA);
        }
        return "\x04" . $this->QA[0]->toBytes(\true) . $this->QA[1]->toBytes(\true);
    }
    public function getParameters($type = 'PKCS1')
    {
        $type = self::validatePlugin('Keys', $type, 'saveParameters');
        $key = $type::saveParameters($this->curve);
        return EC::load($key, 'PKCS1')->withHash($this->hash->getHash())->withSignatureFormat($this->shortFormat);
    }
    public function withSignatureFormat($format)
    {
        if ($this->curve instanceof MontgomeryCurve) {
            throw new UnsupportedOperationException('Montgomery Curves cannot be used to create signatures');
        }
        $new = clone $this;
        $new->shortFormat = $format;
        $new->sigFormat = self::validatePlugin('Signature', $format);
        return $new;
    }
    public function getSignatureFormat()
    {
        return $this->shortFormat;
    }
    public function withContext($context = null)
    {
        if (!$this->curve instanceof TwistedEdwardsCurve) {
            throw new UnsupportedCurveException('Only Ed25519 and Ed448 support contexts');
        }
        $new = clone $this;
        if (!isset($context)) {
            $new->context = null;
            return $new;
        }
        if (!is_string($context)) {
            throw new InvalidArgumentException('setContext expects a string');
        }
        if (strlen($context) > 255) {
            throw new LengthException('The context is supposed to be, at most, 255 bytes long');
        }
        $new->context = $context;
        return $new;
    }
    public function getContext()
    {
        return $this->context;
    }
    public function withHash($hash)
    {
        if ($this->curve instanceof MontgomeryCurve) {
            throw new UnsupportedOperationException('Montgomery Curves cannot be used to create signatures');
        }
        if ($this->curve instanceof Ed25519 && $hash != 'sha512') {
            throw new UnsupportedAlgorithmException('Ed25519 only supports sha512 as a hash');
        }
        if ($this->curve instanceof Ed448 && $hash != 'shake256-912') {
            throw new UnsupportedAlgorithmException('Ed448 only supports shake256 with a length of 114 bytes');
        }
        return parent::withHash($hash);
    }
    public function __toString()
    {
        if ($this->curve instanceof MontgomeryCurve) {
            return '';
        }
        return parent::__toString();
    }
}
