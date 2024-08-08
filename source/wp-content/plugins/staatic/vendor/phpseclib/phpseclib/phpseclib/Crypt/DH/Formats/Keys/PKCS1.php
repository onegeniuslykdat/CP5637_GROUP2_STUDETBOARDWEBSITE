<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DH\Formats\Keys;

use RuntimeException;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\DHParameter;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\PKCS1 as Progenitor;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class PKCS1 extends Progenitor
{
    public static function load($key, $password = '')
    {
        $key = parent::load($key, $password);
        $decoded = ASN1::decodeBER($key);
        if (!$decoded) {
            throw new RuntimeException('Unable to decode BER');
        }
        $components = ASN1::asn1map($decoded[0], DHParameter::MAP);
        if (!is_array($components)) {
            throw new RuntimeException('Unable to perform ASN1 mapping on parameters');
        }
        return $components;
    }
    /**
     * @param BigInteger $prime
     * @param BigInteger $base
     * @param mixed[] $options
     */
    public static function saveParameters($prime, $base, $options = [])
    {
        $params = ['prime' => $prime, 'base' => $base];
        $params = ASN1::encodeDER($params, DHParameter::MAP);
        return "-----BEGIN DH PARAMETERS-----\r\n" . chunk_split(base64_encode($params), 64) . "-----END DH PARAMETERS-----\r\n";
    }
}
