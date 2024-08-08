<?php

namespace Staatic\Vendor\phpseclib3\Crypt\Common;

use ReflectionClass;
use RuntimeException;
use Exception;
use DirectoryIterator;
use Staatic\Vendor\phpseclib3\Crypt\DSA;
use Staatic\Vendor\phpseclib3\Crypt\Hash;
use Staatic\Vendor\phpseclib3\Crypt\RSA;
use Staatic\Vendor\phpseclib3\Exception\NoKeyLoadedException;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedFormatException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class AsymmetricKey
{
    protected static $zero;
    protected static $one;
    protected $format;
    protected $hash;
    private $hmac;
    private static $plugins = [];
    private static $invisiblePlugins = [];
    protected static $engines = [];
    private $comment;
    /**
     * @param mixed[] $options
     */
    abstract public function toString($type, $options = []);
    protected function __construct()
    {
        self::initialize_static_variables();
        $this->hash = new Hash('sha256');
        $this->hmac = new Hash('sha256');
    }
    protected static function initialize_static_variables()
    {
        if (!isset(self::$zero)) {
            self::$zero = new BigInteger(0);
            self::$one = new BigInteger(1);
        }
        self::loadPlugins('Keys');
        if (static::ALGORITHM != 'RSA' && static::ALGORITHM != 'DH') {
            self::loadPlugins('Signature');
        }
    }
    public static function load($key, $password = \false)
    {
        self::initialize_static_variables();
        $class = new ReflectionClass(static::class);
        if ($class->isFinal()) {
            throw new RuntimeException('load() should not be called from final classes (' . static::class . ')');
        }
        $components = \false;
        foreach (self::$plugins[static::ALGORITHM]['Keys'] as $format) {
            if (isset(self::$invisiblePlugins[static::ALGORITHM]) && in_array($format, self::$invisiblePlugins[static::ALGORITHM])) {
                continue;
            }
            try {
                $components = $format::load($key, $password);
            } catch (Exception $e) {
                $components = \false;
            }
            if ($components !== \false) {
                break;
            }
        }
        if ($components === \false) {
            throw new NoKeyLoadedException('Unable to read key');
        }
        $components['format'] = $format;
        $components['secret'] = isset($components['secret']) ? $components['secret'] : '';
        $comment = isset($components['comment']) ? $components['comment'] : null;
        $new = static::onLoad($components);
        $new->format = $format;
        $new->comment = $comment;
        return ($new instanceof PrivateKey) ? $new->withPassword($password) : $new;
    }
    public static function loadPrivateKey($key, $password = '')
    {
        $key = self::load($key, $password);
        if (!$key instanceof PrivateKey) {
            throw new NoKeyLoadedException('The key that was loaded was not a private key');
        }
        return $key;
    }
    public static function loadPublicKey($key)
    {
        $key = self::load($key);
        if (!$key instanceof PublicKey) {
            throw new NoKeyLoadedException('The key that was loaded was not a public key');
        }
        return $key;
    }
    public static function loadParameters($key)
    {
        $key = self::load($key);
        if (!$key instanceof PrivateKey && !$key instanceof PublicKey) {
            throw new NoKeyLoadedException('The key that was loaded was not a parameter');
        }
        return $key;
    }
    public static function loadFormat($type, $key, $password = \false)
    {
        self::initialize_static_variables();
        $components = \false;
        $format = strtolower($type);
        if (isset(self::$plugins[static::ALGORITHM]['Keys'][$format])) {
            $format = self::$plugins[static::ALGORITHM]['Keys'][$format];
            $components = $format::load($key, $password);
        }
        if ($components === \false) {
            throw new NoKeyLoadedException('Unable to read key');
        }
        $components['format'] = $format;
        $components['secret'] = isset($components['secret']) ? $components['secret'] : '';
        $new = static::onLoad($components);
        $new->format = $format;
        return ($new instanceof PrivateKey) ? $new->withPassword($password) : $new;
    }
    public static function loadPrivateKeyFormat($type, $key, $password = \false)
    {
        $key = self::loadFormat($type, $key, $password);
        if (!$key instanceof PrivateKey) {
            throw new NoKeyLoadedException('The key that was loaded was not a private key');
        }
        return $key;
    }
    public static function loadPublicKeyFormat($type, $key)
    {
        $key = self::loadFormat($type, $key);
        if (!$key instanceof PublicKey) {
            throw new NoKeyLoadedException('The key that was loaded was not a public key');
        }
        return $key;
    }
    public static function loadParametersFormat($type, $key)
    {
        $key = self::loadFormat($type, $key);
        if (!$key instanceof PrivateKey && !$key instanceof PublicKey) {
            throw new NoKeyLoadedException('The key that was loaded was not a parameter');
        }
        return $key;
    }
    protected static function validatePlugin($format, $type, $method = null)
    {
        $type = strtolower($type);
        if (!isset(self::$plugins[static::ALGORITHM][$format][$type])) {
            throw new UnsupportedFormatException("{$type} is not a supported format");
        }
        $type = self::$plugins[static::ALGORITHM][$format][$type];
        if (isset($method) && !method_exists($type, $method)) {
            throw new UnsupportedFormatException("{$type} does not implement {$method}");
        }
        return $type;
    }
    private static function loadPlugins($format)
    {
        if (!isset(self::$plugins[static::ALGORITHM][$format])) {
            self::$plugins[static::ALGORITHM][$format] = [];
            foreach (new DirectoryIterator(__DIR__ . '/../' . static::ALGORITHM . '/Formats/' . $format . '/') as $file) {
                if ($file->getExtension() != 'php') {
                    continue;
                }
                $name = $file->getBasename('.php');
                if ($name[0] == '.') {
                    continue;
                }
                $type = 'Staatic\Vendor\phpseclib3\Crypt\\' . static::ALGORITHM . '\Formats\\' . $format . '\\' . $name;
                $reflect = new ReflectionClass($type);
                if ($reflect->isTrait()) {
                    continue;
                }
                self::$plugins[static::ALGORITHM][$format][strtolower($name)] = $type;
                if ($reflect->hasConstant('IS_INVISIBLE')) {
                    self::$invisiblePlugins[static::ALGORITHM][] = $type;
                }
            }
        }
    }
    public static function getSupportedKeyFormats()
    {
        self::initialize_static_variables();
        return self::$plugins[static::ALGORITHM]['Keys'];
    }
    public static function addFileFormat($fullname)
    {
        self::initialize_static_variables();
        if (class_exists($fullname)) {
            $meta = new ReflectionClass($fullname);
            $shortname = $meta->getShortName();
            self::$plugins[static::ALGORITHM]['Keys'][strtolower($shortname)] = $fullname;
            if ($meta->hasConstant('IS_INVISIBLE')) {
                self::$invisiblePlugins[static::ALGORITHM][] = strtolower($shortname);
            }
        }
    }
    public function getLoadedFormat()
    {
        if (empty($this->format)) {
            throw new NoKeyLoadedException('This key was created with createKey - it was not loaded with load. Therefore there is no "loaded format"');
        }
        $meta = new ReflectionClass($this->format);
        return $meta->getShortName();
    }
    public function getComment()
    {
        return $this->comment;
    }
    public static function useBestEngine()
    {
        static::$engines = ['PHP' => \true, 'OpenSSL' => extension_loaded('openssl'), 'libsodium' => function_exists('sodium_crypto_sign_keypair')];
        return static::$engines;
    }
    public static function useInternalEngine()
    {
        static::$engines = ['PHP' => \true, 'OpenSSL' => \false, 'libsodium' => \false];
    }
    public function __toString()
    {
        return $this->toString('PKCS8');
    }
    public function withHash($hash)
    {
        $new = clone $this;
        $new->hash = new Hash($hash);
        $new->hmac = new Hash($hash);
        return $new;
    }
    public function getHash()
    {
        return clone $this->hash;
    }
    protected function computek($h1)
    {
        $v = str_repeat("\x01", strlen($h1));
        $k = str_repeat("\x00", strlen($h1));
        $x = $this->int2octets($this->x);
        $h1 = $this->bits2octets($h1);
        $this->hmac->setKey($k);
        $k = $this->hmac->hash($v . "\x00" . $x . $h1);
        $this->hmac->setKey($k);
        $v = $this->hmac->hash($v);
        $k = $this->hmac->hash($v . "\x01" . $x . $h1);
        $this->hmac->setKey($k);
        $v = $this->hmac->hash($v);
        $qlen = $this->q->getLengthInBytes();
        while (\true) {
            $t = '';
            while (strlen($t) < $qlen) {
                $v = $this->hmac->hash($v);
                $t = $t . $v;
            }
            $k = $this->bits2int($t);
            if (!$k->equals(self::$zero) && $k->compare($this->q) < 0) {
                break;
            }
            $k = $this->hmac->hash($v . "\x00");
            $this->hmac->setKey($k);
            $v = $this->hmac->hash($v);
        }
        return $k;
    }
    private function int2octets($v)
    {
        $out = $v->toBytes();
        $rolen = $this->q->getLengthInBytes();
        if (strlen($out) < $rolen) {
            return str_pad($out, $rolen, "\x00", \STR_PAD_LEFT);
        } elseif (strlen($out) > $rolen) {
            return substr($out, -$rolen);
        } else {
            return $out;
        }
    }
    protected function bits2int($in)
    {
        $v = new BigInteger($in, 256);
        $vlen = strlen($in) << 3;
        $qlen = $this->q->getLength();
        if ($vlen > $qlen) {
            return $v->bitwise_rightShift($vlen - $qlen);
        }
        return $v;
    }
    private function bits2octets($in)
    {
        $z1 = $this->bits2int($in);
        $z2 = $z1->subtract($this->q);
        return ($z2->compare(self::$zero) < 0) ? $this->int2octets($z1) : $this->int2octets($z2);
    }
}
