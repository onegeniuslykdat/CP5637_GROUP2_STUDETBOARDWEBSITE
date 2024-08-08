<?php

namespace Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys;

use UnexpectedValueException;
use DOMDocument;
use DOMXPath;
use InvalidArgumentException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Exception\BadConfigurationException;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedFormatException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
abstract class XML
{
    public static function load($key, $password = '')
    {
        if (!Strings::is_stringable($key)) {
            throw new UnexpectedValueException('Key should be a string - not a ' . gettype($key));
        }
        if (!class_exists('DOMDocument')) {
            throw new BadConfigurationException('The dom extension is not setup correctly on this system');
        }
        $components = ['isPublicKey' => \false, 'primes' => [], 'exponents' => [], 'coefficients' => []];
        $use_errors = libxml_use_internal_errors(\true);
        $dom = new DOMDocument();
        if (substr($key, 0, 5) != '<?xml') {
            $key = '<xml>' . $key . '</xml>';
        }
        if (!$dom->loadXML($key)) {
            libxml_use_internal_errors($use_errors);
            throw new UnexpectedValueException('Key does not appear to contain XML');
        }
        $xpath = new DOMXPath($dom);
        $keys = ['modulus', 'exponent', 'p', 'q', 'dp', 'dq', 'inverseq', 'd'];
        foreach ($keys as $key) {
            $temp = $xpath->query("//*[translate(local-name(), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='{$key}']");
            if (!$temp->length) {
                continue;
            }
            $value = new BigInteger(Strings::base64_decode($temp->item(0)->nodeValue), 256);
            switch ($key) {
                case 'modulus':
                    $components['modulus'] = $value;
                    break;
                case 'exponent':
                    $components['publicExponent'] = $value;
                    break;
                case 'p':
                    $components['primes'][1] = $value;
                    break;
                case 'q':
                    $components['primes'][2] = $value;
                    break;
                case 'dp':
                    $components['exponents'][1] = $value;
                    break;
                case 'dq':
                    $components['exponents'][2] = $value;
                    break;
                case 'inverseq':
                    $components['coefficients'][2] = $value;
                    break;
                case 'd':
                    $components['privateExponent'] = $value;
            }
        }
        libxml_use_internal_errors($use_errors);
        foreach ($components as $key => $value) {
            if (is_array($value) && !count($value)) {
                unset($components[$key]);
            }
        }
        if (isset($components['modulus']) && isset($components['publicExponent'])) {
            if (count($components) == 3) {
                $components['isPublicKey'] = \true;
            }
            return $components;
        }
        throw new UnexpectedValueException('Modulus / exponent not present');
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     * @param BigInteger $d
     * @param mixed[] $primes
     * @param mixed[] $exponents
     * @param mixed[] $coefficients
     */
    public static function savePrivateKey($n, $e, $d, $primes, $exponents, $coefficients, $password = '')
    {
        if (count($primes) != 2) {
            throw new InvalidArgumentException('XML does not support multi-prime RSA keys');
        }
        if (!empty($password) && is_string($password)) {
            throw new UnsupportedFormatException('XML private keys do not support encryption');
        }
        return "<RSAKeyPair>\r\n" . '  <Modulus>' . Strings::base64_encode($n->toBytes()) . "</Modulus>\r\n" . '  <Exponent>' . Strings::base64_encode($e->toBytes()) . "</Exponent>\r\n" . '  <P>' . Strings::base64_encode($primes[1]->toBytes()) . "</P>\r\n" . '  <Q>' . Strings::base64_encode($primes[2]->toBytes()) . "</Q>\r\n" . '  <DP>' . Strings::base64_encode($exponents[1]->toBytes()) . "</DP>\r\n" . '  <DQ>' . Strings::base64_encode($exponents[2]->toBytes()) . "</DQ>\r\n" . '  <InverseQ>' . Strings::base64_encode($coefficients[2]->toBytes()) . "</InverseQ>\r\n" . '  <D>' . Strings::base64_encode($d->toBytes()) . "</D>\r\n" . '</RSAKeyPair>';
    }
    /**
     * @param BigInteger $n
     * @param BigInteger $e
     */
    public static function savePublicKey($n, $e)
    {
        return "<RSAKeyValue>\r\n" . '  <Modulus>' . Strings::base64_encode($n->toBytes()) . "</Modulus>\r\n" . '  <Exponent>' . Strings::base64_encode($e->toBytes()) . "</Exponent>\r\n" . '</RSAKeyValue>';
    }
}
