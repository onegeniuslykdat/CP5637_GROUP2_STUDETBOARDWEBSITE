<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC;

use Staatic\Vendor\phpseclib3\Crypt\Common\Traits\PasswordProtected;
use RuntimeException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common;
use Staatic\Vendor\phpseclib3\Crypt\EC;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Montgomery as MontgomeryCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards as TwistedEdwardsCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Curve25519;
use Staatic\Vendor\phpseclib3\Crypt\EC\Curves\Ed25519;
use Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Keys\PKCS1;
use Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Signature\ASN1 as ASN1Signature;
use Staatic\Vendor\phpseclib3\Crypt\Hash;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedOperationException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
final class PrivateKey extends EC implements Common\PrivateKey
{
    use PasswordProtected;
    protected $dA;
    protected $secret;
    public function multiply($coordinates)
    {
        if ($this->curve instanceof MontgomeryCurve) {
            if ($this->curve instanceof Curve25519 && self::$engines['libsodium']) {
                return sodium_crypto_scalarmult($this->dA->toBytes(), $coordinates);
            }
            $point = [$this->curve->convertInteger(new BigInteger(strrev($coordinates), 256))];
            $point = $this->curve->multiplyPoint($point, $this->dA);
            return strrev($point[0]->toBytes(\true));
        }
        if (!$this->curve instanceof TwistedEdwardsCurve) {
            $coordinates = "\x00{$coordinates}";
        }
        $point = PKCS1::extractPoint($coordinates, $this->curve);
        $point = $this->curve->multiplyPoint($point, $this->dA);
        if ($this->curve instanceof TwistedEdwardsCurve) {
            return $this->curve->encodePoint($point);
        }
        if (empty($point)) {
            throw new RuntimeException('The infinity point is invalid');
        }
        return "\x04" . $point[0]->toBytes(\true) . $point[1]->toBytes(\true);
    }
    public function sign($message)
    {
        if ($this->curve instanceof MontgomeryCurve) {
            throw new UnsupportedOperationException('Montgomery Curves cannot be used to create signatures');
        }
        $dA = $this->dA;
        $order = $this->curve->getOrder();
        $shortFormat = $this->shortFormat;
        $format = $this->sigFormat;
        if ($format === \false) {
            return \false;
        }
        if ($this->curve instanceof TwistedEdwardsCurve) {
            if ($this->curve instanceof Ed25519 && self::$engines['libsodium'] && !isset($this->context)) {
                $result = sodium_crypto_sign_detached($message, $this->withPassword()->toString('libsodium'));
                return ($shortFormat == 'SSH2') ? Strings::packSSH2('ss', 'ssh-' . strtolower($this->getCurve()), $result) : $result;
            }
            $A = $this->curve->encodePoint($this->QA);
            $curve = $this->curve;
            $hash = new Hash($curve::HASH);
            $secret = substr($hash->hash($this->secret), $curve::SIZE);
            if ($curve instanceof Ed25519) {
                $dom = (!isset($this->context)) ? '' : ('SigEd25519 no Ed25519 collisions' . "\x00" . chr(strlen($this->context)) . $this->context);
            } else {
                $context = isset($this->context) ? $this->context : '';
                $dom = 'SigEd448' . "\x00" . chr(strlen($context)) . $context;
            }
            $r = $hash->hash($dom . $secret . $message);
            $r = strrev($r);
            $r = new BigInteger($r, 256);
            list(, $r) = $r->divide($order);
            $R = $curve->multiplyPoint($curve->getBasePoint(), $r);
            $R = $curve->encodePoint($R);
            $k = $hash->hash($dom . $R . $A . $message);
            $k = strrev($k);
            $k = new BigInteger($k, 256);
            list(, $k) = $k->divide($order);
            $S = $k->multiply($dA)->add($r);
            list(, $S) = $S->divide($order);
            $S = str_pad(strrev($S->toBytes()), $curve::SIZE, "\x00");
            return ($shortFormat == 'SSH2') ? Strings::packSSH2('ss', 'ssh-' . strtolower($this->getCurve()), $R . $S) : ($R . $S);
        }
        if (self::$engines['OpenSSL'] && in_array($this->hash->getHash(), openssl_get_md_methods())) {
            $signature = '';
            $result = openssl_sign($message, $signature, $this->withPassword()->toString('PKCS8', ['namedCurve' => \false]), $this->hash->getHash());
            if ($result) {
                if ($shortFormat == 'ASN1') {
                    return $signature;
                }
                extract(ASN1Signature::load($signature));
                return ($shortFormat == 'SSH2') ? $format::save($r, $s, $this->getCurve()) : $format::save($r, $s);
            }
        }
        $e = $this->hash->hash($message);
        $e = new BigInteger($e, 256);
        $Ln = $this->hash->getLength() - $order->getLength();
        $z = ($Ln > 0) ? $e->bitwise_rightShift($Ln) : $e;
        while (\true) {
            $k = BigInteger::randomRange(self::$one, $order->subtract(self::$one));
            list($x, $y) = $this->curve->multiplyPoint($this->curve->getBasePoint(), $k);
            $x = $x->toBigInteger();
            list(, $r) = $x->divide($order);
            if ($r->equals(self::$zero)) {
                continue;
            }
            $kinv = $k->modInverse($order);
            $temp = $z->add($dA->multiply($r));
            $temp = $kinv->multiply($temp);
            list(, $s) = $temp->divide($order);
            if (!$s->equals(self::$zero)) {
                break;
            }
        }
        return ($shortFormat == 'SSH2') ? $format::save($r, $s, $this->getCurve()) : $format::save($r, $s);
    }
    /**
     * @param mixed[] $options
     */
    public function toString($type, $options = [])
    {
        $type = self::validatePlugin('Keys', $type, 'savePrivateKey');
        return $type::savePrivateKey($this->dA, $this->curve, $this->QA, $this->secret, $this->password, $options);
    }
    public function getPublicKey()
    {
        $format = 'PKCS8';
        if ($this->curve instanceof MontgomeryCurve) {
            $format = 'MontgomeryPublic';
        }
        $type = self::validatePlugin('Keys', $format, 'savePublicKey');
        $key = $type::savePublicKey($this->curve, $this->QA);
        $key = EC::loadFormat($format, $key);
        if ($this->curve instanceof MontgomeryCurve) {
            return $key;
        }
        $key = $key->withHash($this->hash->getHash())->withSignatureFormat($this->shortFormat);
        if ($this->curve instanceof TwistedEdwardsCurve) {
            $key = $key->withContext($this->context);
        }
        return $key;
    }
}
