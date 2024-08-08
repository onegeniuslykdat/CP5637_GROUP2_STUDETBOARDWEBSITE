<?php

namespace Staatic\Vendor\phpseclib3\Crypt\RSA;

use Staatic\Vendor\phpseclib3\Crypt\Common\Traits\Fingerprint;
use LengthException;
use OutOfRangeException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\Common;
use Staatic\Vendor\phpseclib3\Crypt\Hash;
use Staatic\Vendor\phpseclib3\Crypt\Random;
use Staatic\Vendor\phpseclib3\Crypt\RSA;
use Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys\PSS;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedAlgorithmException;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedFormatException;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\DigestInfo;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
final class PublicKey extends RSA implements Common\PublicKey
{
    use Fingerprint;
    private function exponentiate(BigInteger $x)
    {
        return $x->modPow($this->exponent, $this->modulus);
    }
    private function rsavp1($s)
    {
        if ($s->compare(self::$zero) < 0 || $s->compare($this->modulus) > 0) {
            return \false;
        }
        return $this->exponentiate($s);
    }
    private function rsassa_pkcs1_v1_5_verify($m, $s)
    {
        if (strlen($s) != $this->k) {
            return \false;
        }
        $s = $this->os2ip($s);
        $m2 = $this->rsavp1($s);
        if ($m2 === \false) {
            return \false;
        }
        $em = $this->i2osp($m2, $this->k);
        if ($em === \false) {
            return \false;
        }
        $exception = \false;
        try {
            $em2 = $this->emsa_pkcs1_v1_5_encode($m, $this->k);
            $r1 = hash_equals($em, $em2);
        } catch (LengthException $e) {
            $exception = \true;
        }
        try {
            $em3 = $this->emsa_pkcs1_v1_5_encode_without_null($m, $this->k);
            $r2 = hash_equals($em, $em3);
        } catch (LengthException $e) {
            $exception = \true;
        } catch (UnsupportedAlgorithmException $e) {
            $r2 = \false;
        }
        if ($exception) {
            throw new LengthException('RSA modulus too short');
        }
        return $r1 || $r2;
    }
    private function rsassa_pkcs1_v1_5_relaxed_verify($m, $s)
    {
        if (strlen($s) != $this->k) {
            return \false;
        }
        $s = $this->os2ip($s);
        $m2 = $this->rsavp1($s);
        if ($m2 === \false) {
            return \false;
        }
        $em = $this->i2osp($m2, $this->k);
        if ($em === \false) {
            return \false;
        }
        if (Strings::shift($em, 2) != "\x00\x01") {
            return \false;
        }
        $em = ltrim($em, "\xff");
        if (Strings::shift($em) != "\x00") {
            return \false;
        }
        $decoded = ASN1::decodeBER($em);
        if (!is_array($decoded) || empty($decoded[0]) || strlen($em) > $decoded[0]['length']) {
            return \false;
        }
        static $oids;
        if (!isset($oids)) {
            $oids = ['md2' => '1.2.840.113549.2.2', 'md4' => '1.2.840.113549.2.4', 'md5' => '1.2.840.113549.2.5', 'id-sha1' => '1.3.14.3.2.26', 'id-sha256' => '2.16.840.1.101.3.4.2.1', 'id-sha384' => '2.16.840.1.101.3.4.2.2', 'id-sha512' => '2.16.840.1.101.3.4.2.3', 'id-sha224' => '2.16.840.1.101.3.4.2.4', 'id-sha512/224' => '2.16.840.1.101.3.4.2.5', 'id-sha512/256' => '2.16.840.1.101.3.4.2.6'];
            ASN1::loadOIDs($oids);
        }
        $decoded = ASN1::asn1map($decoded[0], DigestInfo::MAP);
        if (!isset($decoded) || $decoded === \false) {
            return \false;
        }
        if (!isset($oids[$decoded['digestAlgorithm']['algorithm']])) {
            return \false;
        }
        if (isset($decoded['digestAlgorithm']['parameters']) && $decoded['digestAlgorithm']['parameters'] !== ['null' => '']) {
            return \false;
        }
        $hash = $decoded['digestAlgorithm']['algorithm'];
        $hash = (substr($hash, 0, 3) == 'id-') ? substr($hash, 3) : $hash;
        $hash = new Hash($hash);
        $em = $hash->hash($m);
        $em2 = $decoded['digest'];
        return hash_equals($em, $em2);
    }
    private function emsa_pss_verify($m, $em, $emBits)
    {
        $emLen = $emBits + 7 >> 3;
        $sLen = ($this->sLen !== null) ? $this->sLen : $this->hLen;
        $mHash = $this->hash->hash($m);
        if ($emLen < $this->hLen + $sLen + 2) {
            return \false;
        }
        if ($em[strlen($em) - 1] != chr(0xbc)) {
            return \false;
        }
        $maskedDB = substr($em, 0, -$this->hLen - 1);
        $h = substr($em, -$this->hLen - 1, $this->hLen);
        $temp = chr(0xff << ($emBits & 7));
        if ((~$maskedDB[0] & $temp) != $temp) {
            return \false;
        }
        $dbMask = $this->mgf1($h, $emLen - $this->hLen - 1);
        $db = $maskedDB ^ $dbMask;
        $db[0] = ~chr(0xff << ($emBits & 7)) & $db[0];
        $temp = $emLen - $this->hLen - $sLen - 2;
        if (substr($db, 0, $temp) != str_repeat(chr(0), $temp) || ord($db[$temp]) != 1) {
            return \false;
        }
        $salt = substr($db, $temp + 1);
        $m2 = "\x00\x00\x00\x00\x00\x00\x00\x00" . $mHash . $salt;
        $h2 = $this->hash->hash($m2);
        return hash_equals($h, $h2);
    }
    private function rsassa_pss_verify($m, $s)
    {
        if (strlen($s) != $this->k) {
            return \false;
        }
        $modBits = strlen($this->modulus->toBits());
        $s2 = $this->os2ip($s);
        $m2 = $this->rsavp1($s2);
        $em = $this->i2osp($m2, $this->k);
        if ($em === \false) {
            return \false;
        }
        return $this->emsa_pss_verify($m, $em, $modBits - 1);
    }
    public function verify($message, $signature)
    {
        switch ($this->signaturePadding) {
            case self::SIGNATURE_RELAXED_PKCS1:
                return $this->rsassa_pkcs1_v1_5_relaxed_verify($message, $signature);
            case self::SIGNATURE_PKCS1:
                return $this->rsassa_pkcs1_v1_5_verify($message, $signature);
            default:
                return $this->rsassa_pss_verify($message, $signature);
        }
    }
    private function rsaes_pkcs1_v1_5_encrypt($m, $pkcs15_compat = \false)
    {
        $mLen = strlen($m);
        if ($mLen > $this->k - 11) {
            throw new LengthException('Message too long');
        }
        $psLen = $this->k - $mLen - 3;
        $ps = '';
        while (strlen($ps) != $psLen) {
            $temp = Random::string($psLen - strlen($ps));
            $temp = str_replace("\x00", '', $temp);
            $ps .= $temp;
        }
        $type = 2;
        $em = chr(0) . chr($type) . $ps . chr(0) . $m;
        $m = $this->os2ip($em);
        $c = $this->rsaep($m);
        $c = $this->i2osp($c, $this->k);
        return $c;
    }
    private function rsaes_oaep_encrypt($m)
    {
        $mLen = strlen($m);
        if ($mLen > $this->k - 2 * $this->hLen - 2) {
            throw new LengthException('Message too long');
        }
        $lHash = $this->hash->hash($this->label);
        $ps = str_repeat(chr(0), $this->k - $mLen - 2 * $this->hLen - 2);
        $db = $lHash . $ps . chr(1) . $m;
        $seed = Random::string($this->hLen);
        $dbMask = $this->mgf1($seed, $this->k - $this->hLen - 1);
        $maskedDB = $db ^ $dbMask;
        $seedMask = $this->mgf1($maskedDB, $this->hLen);
        $maskedSeed = $seed ^ $seedMask;
        $em = chr(0) . $maskedSeed . $maskedDB;
        $m = $this->os2ip($em);
        $c = $this->rsaep($m);
        $c = $this->i2osp($c, $this->k);
        return $c;
    }
    private function rsaep($m)
    {
        if ($m->compare(self::$zero) < 0 || $m->compare($this->modulus) > 0) {
            throw new OutOfRangeException('Message representative out of range');
        }
        return $this->exponentiate($m);
    }
    private function raw_encrypt($m)
    {
        if (strlen($m) > $this->k) {
            throw new LengthException('Message too long');
        }
        $temp = $this->os2ip($m);
        $temp = $this->rsaep($temp);
        return $this->i2osp($temp, $this->k);
    }
    public function encrypt($plaintext)
    {
        switch ($this->encryptionPadding) {
            case self::ENCRYPTION_NONE:
                return $this->raw_encrypt($plaintext);
            case self::ENCRYPTION_PKCS1:
                return $this->rsaes_pkcs1_v1_5_encrypt($plaintext);
            default:
                return $this->rsaes_oaep_encrypt($plaintext);
        }
    }
    /**
     * @param mixed[] $options
     */
    public function toString($type, $options = [])
    {
        $type = self::validatePlugin('Keys', $type, 'savePublicKey');
        if ($type == PSS::class) {
            if ($this->signaturePadding == self::SIGNATURE_PSS) {
                $options += ['hash' => $this->hash->getHash(), 'MGFHash' => $this->mgfHash->getHash(), 'saltLength' => $this->getSaltLength()];
            } else {
                throw new UnsupportedFormatException('The PSS format can only be used when the signature method has been explicitly set to PSS');
            }
        }
        return $type::savePublicKey($this->modulus, $this->publicExponent, $options);
    }
    public function asPrivateKey()
    {
        $new = new PrivateKey();
        $new->exponent = $this->exponent;
        $new->modulus = $this->modulus;
        $new->k = $this->k;
        $new->format = $this->format;
        return $new->withHash($this->hash->getHash())->withMGFHash($this->mgfHash->getHash())->withSaltLength($this->sLen)->withLabel($this->label)->withPadding($this->signaturePadding | $this->encryptionPadding);
    }
}
