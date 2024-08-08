<?php

namespace Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys;

use UnexpectedValueException;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\RSASSA_PSS_params;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\HashAlgorithm;
use Staatic\Vendor\phpseclib3\File\ASN1\Element;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common\Formats\Keys\PKCS8 as Progenitor;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class PSS extends Progenitor
{
    const OID_NAME = 'id-RSASSA-PSS';
    const OID_VALUE = '1.2.840.113549.1.1.10';
    private static $oidsLoaded = \false;
    protected static $childOIDsLoaded = \false;
    private static function initialize_static_variables()
    {
        if (!self::$oidsLoaded) {
            ASN1::loadOIDs(['md2' => '1.2.840.113549.2.2', 'md4' => '1.2.840.113549.2.4', 'md5' => '1.2.840.113549.2.5', 'id-sha1' => '1.3.14.3.2.26', 'id-sha256' => '2.16.840.1.101.3.4.2.1', 'id-sha384' => '2.16.840.1.101.3.4.2.2', 'id-sha512' => '2.16.840.1.101.3.4.2.3', 'id-sha224' => '2.16.840.1.101.3.4.2.4', 'id-sha512/224' => '2.16.840.1.101.3.4.2.5', 'id-sha512/256' => '2.16.840.1.101.3.4.2.6', 'id-mgf1' => '1.2.840.113549.1.1.8']);
            self::$oidsLoaded = \true;
        }
    }
    public static function load($key, $password = '')
    {
        self::initialize_static_variables();
        if (!Strings::is_stringable($key)) {
            throw new UnexpectedValueException('Key should be a string - not a ' . gettype($key));
        }
        $components = ['isPublicKey' => strpos($key, 'PUBLIC') !== \false];
        $key = parent::load($key, $password);
        $type = isset($key['privateKey']) ? 'private' : 'public';
        $result = $components + PKCS1::load($key[$type . 'Key']);
        if (isset($key[$type . 'KeyAlgorithm']['parameters'])) {
            $decoded = ASN1::decodeBER($key[$type . 'KeyAlgorithm']['parameters']);
            if ($decoded === \false) {
                throw new UnexpectedValueException('Unable to decode parameters');
            }
            $params = ASN1::asn1map($decoded[0], RSASSA_PSS_params::MAP);
        } else {
            $params = [];
        }
        if (isset($params['maskGenAlgorithm']['parameters'])) {
            $decoded = ASN1::decodeBER($params['maskGenAlgorithm']['parameters']);
            if ($decoded === \false) {
                throw new UnexpectedValueException('Unable to decode parameters');
            }
            $params['maskGenAlgorithm']['parameters'] = ASN1::asn1map($decoded[0], HashAlgorithm::MAP);
        } else {
            $params['maskGenAlgorithm'] = ['algorithm' => 'id-mgf1', 'parameters' => ['algorithm' => 'id-sha1']];
        }
        if (!isset($params['hashAlgorithm']['algorithm'])) {
            $params['hashAlgorithm']['algorithm'] = 'id-sha1';
        }
        $result['hash'] = str_replace('id-', '', $params['hashAlgorithm']['algorithm']);
        $result['MGFHash'] = str_replace('id-', '', $params['maskGenAlgorithm']['parameters']['algorithm']);
        if (isset($params['saltLength'])) {
            $result['saltLength'] = (int) $params['saltLength']->toString();
        }
        if (isset($key['meta'])) {
            $result['meta'] = $key['meta'];
        }
        return $result;
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     * @param BigInteger $d
     * @param mixed[] $primes
     * @param mixed[] $exponents
     * @param mixed[] $coefficients
     * @param mixed[] $options
     */
    public static function savePrivateKey($n, $e, $d, $primes, $exponents, $coefficients, $password = '', $options = [])
    {
        self::initialize_static_variables();
        $key = PKCS1::savePrivateKey($n, $e, $d, $primes, $exponents, $coefficients);
        $key = ASN1::extractBER($key);
        $params = self::savePSSParams($options);
        return self::wrapPrivateKey($key, [], $params, $password, null, '', $options);
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     * @param mixed[] $options
     */
    public static function savePublicKey($n, $e, $options = [])
    {
        self::initialize_static_variables();
        $key = PKCS1::savePublicKey($n, $e);
        $key = ASN1::extractBER($key);
        $params = self::savePSSParams($options);
        return self::wrapPublicKey($key, $params);
    }
    /**
     * @param mixed[] $options
     */
    public static function savePSSParams($options)
    {
        $params = ['trailerField' => new BigInteger(1)];
        if (isset($options['hash'])) {
            $params['hashAlgorithm']['algorithm'] = 'id-' . $options['hash'];
        }
        if (isset($options['MGFHash'])) {
            $temp = ['algorithm' => 'id-' . $options['MGFHash']];
            $temp = ASN1::encodeDER($temp, HashAlgorithm::MAP);
            $params['maskGenAlgorithm'] = ['algorithm' => 'id-mgf1', 'parameters' => new Element($temp)];
        }
        if (isset($options['saltLength'])) {
            $params['saltLength'] = new BigInteger($options['saltLength']);
        }
        return new Element(ASN1::encodeDER($params, RSASSA_PSS_params::MAP));
    }
}
