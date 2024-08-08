<?php

namespace Staatic\Vendor\phpseclib3\Crypt;

use ReflectionClass;
use RuntimeException;
use InvalidArgumentException;
use Staatic\Vendor\phpseclib3\Crypt\Common\AsymmetricKey;
use Staatic\Vendor\phpseclib3\Crypt\DSA\Parameters;
use Staatic\Vendor\phpseclib3\Crypt\DSA\PrivateKey;
use Staatic\Vendor\phpseclib3\Crypt\DSA\PublicKey;
use Staatic\Vendor\phpseclib3\Exception\InsufficientSetupException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class DSA extends AsymmetricKey
{
    const ALGORITHM = 'DSA';
    protected $p;
    protected $q;
    protected $g;
    protected $y;
    protected $sigFormat;
    protected $shortFormat;
    public static function createParameters($L = 2048, $N = 224)
    {
        self::initialize_static_variables();
        $class = new ReflectionClass(static::class);
        if ($class->isFinal()) {
            throw new RuntimeException('createParameters() should not be called from final classes (' . static::class . ')');
        }
        if (!isset(self::$engines['PHP'])) {
            self::useBestEngine();
        }
        switch (\true) {
            case $N == 160:
            case $L == 2048 && $N == 224:
            case $L == 2048 && $N == 256:
            case $L == 3072 && $N == 256:
                break;
            default:
                throw new InvalidArgumentException('Invalid values for N and L');
        }
        $two = new BigInteger(2);
        $q = BigInteger::randomPrime($N);
        $divisor = $q->multiply($two);
        do {
            $x = BigInteger::random($L);
            list(, $c) = $x->divide($divisor);
            $p = $x->subtract($c->subtract(self::$one));
        } while ($p->getLength() != $L || !$p->isPrime());
        $p_1 = $p->subtract(self::$one);
        list($e) = $p_1->divide($q);
        $h = clone $two;
        while (\true) {
            $g = $h->powMod($e, $p);
            if (!$g->equals(self::$one)) {
                break;
            }
            $h = $h->add(self::$one);
        }
        $dsa = new Parameters();
        $dsa->p = $p;
        $dsa->q = $q;
        $dsa->g = $g;
        return $dsa;
    }
    public static function createKey(...$args)
    {
        self::initialize_static_variables();
        $class = new ReflectionClass(static::class);
        if ($class->isFinal()) {
            throw new RuntimeException('createKey() should not be called from final classes (' . static::class . ')');
        }
        if (!isset(self::$engines['PHP'])) {
            self::useBestEngine();
        }
        if (count($args) == 2 && is_int($args[0]) && is_int($args[1])) {
            $params = self::createParameters($args[0], $args[1]);
        } elseif (count($args) == 1 && $args[0] instanceof Parameters) {
            $params = $args[0];
        } elseif (!count($args)) {
            $params = self::createParameters();
        } else {
            throw new InsufficientSetupException('Valid parameters are either two integers (L and N), a single DSA object or no parameters at all.');
        }
        $private = new PrivateKey();
        $private->p = $params->p;
        $private->q = $params->q;
        $private->g = $params->g;
        $private->x = BigInteger::randomRange(self::$one, $private->q->subtract(self::$one));
        $private->y = $private->g->powMod($private->x, $private->p);
        return $private->withHash($params->hash->getHash())->withSignatureFormat($params->shortFormat);
    }
    /**
     * @param mixed[] $components
     */
    protected static function onLoad($components)
    {
        if (!isset(self::$engines['PHP'])) {
            self::useBestEngine();
        }
        if (!isset($components['x']) && !isset($components['y'])) {
            $new = new Parameters();
        } elseif (isset($components['x'])) {
            $new = new PrivateKey();
            $new->x = $components['x'];
        } else {
            $new = new PublicKey();
        }
        $new->p = $components['p'];
        $new->q = $components['q'];
        $new->g = $components['g'];
        if (isset($components['y'])) {
            $new->y = $components['y'];
        }
        return $new;
    }
    protected function __construct()
    {
        $this->sigFormat = self::validatePlugin('Signature', 'ASN1');
        $this->shortFormat = 'ASN1';
        parent::__construct();
    }
    public function getLength()
    {
        return ['L' => $this->p->getLength(), 'N' => $this->q->getLength()];
    }
    public function getEngine()
    {
        if (!isset(self::$engines['PHP'])) {
            self::useBestEngine();
        }
        return (self::$engines['OpenSSL'] && in_array($this->hash->getHash(), openssl_get_md_methods())) ? 'OpenSSL' : 'PHP';
    }
    public function getParameters()
    {
        $type = self::validatePlugin('Keys', 'PKCS1', 'saveParameters');
        $key = $type::saveParameters($this->p, $this->q, $this->g);
        return DSA::load($key, 'PKCS1')->withHash($this->hash->getHash())->withSignatureFormat($this->shortFormat);
    }
    public function withSignatureFormat($format)
    {
        $new = clone $this;
        $new->shortFormat = $format;
        $new->sigFormat = self::validatePlugin('Signature', $format);
        return $new;
    }
    public function getSignatureFormat()
    {
        return $this->shortFormat;
    }
}
