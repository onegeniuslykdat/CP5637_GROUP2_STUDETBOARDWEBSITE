<?php

namespace Staatic\Vendor\phpseclib3\Crypt;

use Exception;
use Staatic\Vendor\phpseclib3\Crypt\Common\AsymmetricKey;
use Staatic\Vendor\phpseclib3\Crypt\Common\PrivateKey;
use Staatic\Vendor\phpseclib3\Crypt\Common\PublicKey;
use Staatic\Vendor\phpseclib3\Exception\NoKeyLoadedException;
use Staatic\Vendor\phpseclib3\File\X509;
abstract class PublicKeyLoader
{
    public static function load($key, $password = \false)
    {
        try {
            return EC::load($key, $password);
        } catch (NoKeyLoadedException $e) {
        }
        try {
            return RSA::load($key, $password);
        } catch (NoKeyLoadedException $e) {
        }
        try {
            return DSA::load($key, $password);
        } catch (NoKeyLoadedException $e) {
        }
        try {
            $x509 = new X509();
            $x509->loadX509($key);
            $key = $x509->getPublicKey();
            if ($key) {
                return $key;
            }
        } catch (Exception $e) {
        }
        throw new NoKeyLoadedException('Unable to read key');
    }
    public static function loadPrivateKey($key, $password = \false)
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
}
