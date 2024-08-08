<?php

namespace Staatic\Vendor\phpseclib3\Crypt;

use Exception;
use Throwable;
use RuntimeException;
abstract class Random
{
    public static function string($length)
    {
        if (!$length) {
            return '';
        }
        try {
            return random_bytes($length);
        } catch (Exception $e) {
        } catch (Throwable $e) {
        }
        static $crypto = \false, $v;
        if ($crypto === \false) {
            $old_session_id = session_id();
            $old_use_cookies = ini_get('session.use_cookies');
            $old_session_cache_limiter = session_cache_limiter();
            $_OLD_SESSION = isset($_SESSION) ? $_SESSION : \false;
            if ($old_session_id != '') {
                session_write_close();
            }
            session_id(1);
            ini_set('session.use_cookies', 0);
            session_cache_limiter('');
            session_start();
            $v = (isset($_SERVER) ? self::safe_serialize($_SERVER) : '') . (isset($_POST) ? self::safe_serialize($_POST) : '') . (isset($_GET) ? self::safe_serialize($_GET) : '') . (isset($_COOKIE) ? self::safe_serialize($_COOKIE) : '') . (version_compare(\PHP_VERSION, '8.1.0', '>=') ? serialize($GLOBALS) : self::safe_serialize($GLOBALS)) . self::safe_serialize($_SESSION) . self::safe_serialize($_OLD_SESSION);
            $v = $seed = $_SESSION['seed'] = sha1($v, \true);
            if (!isset($_SESSION['count'])) {
                $_SESSION['count'] = 0;
            }
            $_SESSION['count']++;
            session_write_close();
            if ($old_session_id != '') {
                session_id($old_session_id);
                session_start();
                ini_set('session.use_cookies', $old_use_cookies);
                session_cache_limiter($old_session_cache_limiter);
            } else if ($_OLD_SESSION !== \false) {
                $_SESSION = $_OLD_SESSION;
                unset($_OLD_SESSION);
            } else {
                unset($_SESSION);
            }
            $key = sha1($seed . 'A', \true);
            $iv = sha1($seed . 'C', \true);
            switch (\true) {
                case class_exists('Staatic\Vendor\phpseclib3\Crypt\AES'):
                    $crypto = new AES('ctr');
                    break;
                case class_exists('Staatic\Vendor\phpseclib3\Crypt\Twofish'):
                    $crypto = new Twofish('ctr');
                    break;
                case class_exists('Staatic\Vendor\phpseclib3\Crypt\Blowfish'):
                    $crypto = new Blowfish('ctr');
                    break;
                case class_exists('Staatic\Vendor\phpseclib3\Crypt\TripleDES'):
                    $crypto = new TripleDES('ctr');
                    break;
                case class_exists('Staatic\Vendor\phpseclib3\Crypt\DES'):
                    $crypto = new DES('ctr');
                    break;
                case class_exists('Staatic\Vendor\phpseclib3\Crypt\RC4'):
                    $crypto = new RC4();
                    break;
                default:
                    throw new RuntimeException(__CLASS__ . ' requires at least one symmetric cipher be loaded');
            }
            $crypto->setKey(substr($key, 0, $crypto->getKeyLength() >> 3));
            $crypto->setIV(substr($iv, 0, $crypto->getBlockLength() >> 3));
            $crypto->enableContinuousBuffer();
        }
        $result = '';
        while (strlen($result) < $length) {
            $i = $crypto->encrypt(microtime());
            $r = $crypto->encrypt($i ^ $v);
            $v = $crypto->encrypt($r ^ $i);
            $result .= $r;
        }
        return substr($result, 0, $length);
    }
    private static function safe_serialize(&$arr)
    {
        if (is_object($arr)) {
            return '';
        }
        if (!is_array($arr)) {
            return serialize($arr);
        }
        if (isset($arr['__phpseclib_marker'])) {
            return '';
        }
        $safearr = [];
        $arr['__phpseclib_marker'] = \true;
        foreach (array_keys($arr) as $key) {
            if ($key !== '__phpseclib_marker') {
                $safearr[$key] = self::safe_serialize($arr[$key]);
            }
        }
        unset($arr['__phpseclib_marker']);
        return serialize($safearr);
    }
}
