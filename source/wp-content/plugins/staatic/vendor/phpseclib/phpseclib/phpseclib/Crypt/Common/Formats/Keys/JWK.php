<?php

namespace Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys;

use UnexpectedValueException;
use Exception;
use RuntimeException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
abstract class JWK
{
    public static function load($key, $password = '')
    {
        if (!Strings::is_stringable($key)) {
            throw new UnexpectedValueException('Key should be a string - not a ' . gettype($key));
        }
        $key = preg_replace('#\s#', '', $key);
        if (\PHP_VERSION_ID >= 73000) {
            $key = json_decode($key, true, 512, 0);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(json_last_error_msg());
            }
        } else {
            $key = json_decode($key);
            if (!$key) {
                throw new RuntimeException('Unable to decode JSON');
            }
        }
        if (isset($key->kty)) {
            return $key;
        }
        if (count($key->keys) != 1) {
            throw new RuntimeException('Although the JWK key format supports multiple keys phpseclib does not');
        }
        return $key->keys[0];
    }
    /**
     * @param mixed[] $key
     * @param mixed[] $options
     */
    protected static function wrapKey($key, $options)
    {
        return json_encode(['keys' => [$key + $options]]);
    }
}
