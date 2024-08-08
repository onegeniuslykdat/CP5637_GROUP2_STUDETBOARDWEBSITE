<?php

namespace Staatic\Vendor\phpseclib3\Crypt\RSA;

use Staatic\Vendor\phpseclib3\Crypt\Common\Traits\PasswordProtected;
use OutOfRangeException;
use LengthException;
use RuntimeException;
use Staatic\Vendor\phpseclib3\Crypt\Common;
use Staatic\Vendor\phpseclib3\Crypt\Random;
use Staatic\Vendor\phpseclib3\Crypt\RSA;
use Staatic\Vendor\phpseclib3\Crypt\RSA\Formats\Keys\PSS;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedFormatException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
final class PrivateKey extends RSA implements Common\PrivateKey
{
    use PasswordProtected;
    protected $primes;
    protected $exponents;
    protected $coefficients;
    protected $privateExponent;
    private function rsadp(BigInteger $c)
    {
        if ($c->compare(self::$zero) < 0 || $c->compare($this->modulus) > 0) {
            throw new OutOfRangeException('Ciphertext representative out of range');
        }
        return $this->exponentiate($c);
    }
    private function rsasp1(BigInteger $m)
    {
        if ($m->compare(self::$zero) < 0 || $m->compare($this->modulus) > 0) {
            throw new OutOfRangeException('Signature representative out of range');
        }
        return $this->exponentiate($m);
    }
    /**
     * @param BigInteger $x
     */
    protected function exponentiate($x)
    {
        switch (\true) {
            case empty($this->primes):
            case $this->primes[1]->equals(self::$zero):
            case empty($this->coefficients):
            case $this->coefficients[2]->equals(self::$zero):
            case empty($this->exponents):
            case $this->exponents[1]->equals(self::$zero):
                return $x->modPow($this->exponent, $this->modulus);
        }
        $num_primes = count($this->primes);
        if (!static::$enableBlinding) {
            $m_i = [1 => $x->modPow($this->exponents[1], $this->primes[1]), 2 => $x->modPow($this->exponents[2], $this->primes[2])];
            $h = $m_i[1]->subtract($m_i[2]);
            $h = $h->multiply($this->coefficients[2]);
            list(, $h) = $h->divide($this->primes[1]);
            $m = $m_i[2]->add($h->multiply($this->primes[2]));
            $r = $this->primes[1];
            for ($i = 3; $i <= $num_primes; $i++) {
                $m_i = $x->modPow($this->exponents[$i], $this->primes[$i]);
                $r = $r->multiply($this->primes[$i - 1]);
                $h = $m_i->subtract($m);
                $h = $h->multiply($this->coefficients[$i]);
                list(, $h) = $h->divide($this->primes[$i]);
                $m = $m->add($r->multiply($h));
            }
        } else {
            $smallest = $this->primes[1];
            for ($i = 2; $i <= $num_primes; $i++) {
                if ($smallest->compare($this->primes[$i]) > 0) {
                    $smallest = $this->primes[$i];
                }
            }
            $r = BigInteger::randomRange(self::$one, $smallest->subtract(self::$one));
            $m_i = [1 => $this->blind($x, $r, 1), 2 => $this->blind($x, $r, 2)];
            $h = $m_i[1]->subtract($m_i[2]);
            $h = $h->multiply($this->coefficients[2]);
            list(, $h) = $h->divide($this->primes[1]);
            $m = $m_i[2]->add($h->multiply($this->primes[2]));
            $r = $this->primes[1];
            for ($i = 3; $i <= $num_primes; $i++) {
                $m_i = $this->blind($x, $r, $i);
                $r = $r->multiply($this->primes[$i - 1]);
                $h = $m_i->subtract($m);
                $h = $h->multiply($this->coefficients[$i]);
                list(, $h) = $h->divide($this->primes[$i]);
                $m = $m->add($r->multiply($h));
            }
        }
        return $m;
    }
    private function blind(BigInteger $x, BigInteger $r, $i)
    {
        $x = $x->multiply($r->modPow($this->publicExponent, $this->primes[$i]));
        $x = $x->modPow($this->exponents[$i], $this->primes[$i]);
        $r = $r->modInverse($this->primes[$i]);
        $x = $x->multiply($r);
        list(, $x) = $x->divide($this->primes[$i]);
        return $x;
    }
    private function emsa_pss_encode($m, $emBits)
    {
        $emLen = $emBits + 1 >> 3;
        $sLen = ($this->sLen !== null) ? $this->sLen : $this->hLen;
        $mHash = $this->hash->hash($m);
        if ($emLen < $this->hLen + $sLen + 2) {
            throw new LengthException('RSA modulus too short');
        }
        $salt = Random::string($sLen);
        $m2 = "\x00\x00\x00\x00\x00\x00\x00\x00" . $mHash . $salt;
        $h = $this->hash->hash($m2);
        $ps = str_repeat(chr(0), $emLen - $sLen - $this->hLen - 2);
        $db = $ps . chr(1) . $salt;
        $dbMask = $this->mgf1($h, $emLen - $this->hLen - 1);
        $maskedDB = $db ^ $dbMask;
        $maskedDB[0] = ~chr(0xff << ($emBits & 7)) & $maskedDB[0];
        $em = $maskedDB . $h . chr(0xbc);
        return $em;
    }
    private function rsassa_pss_sign($m)
    {
        $em = $this->emsa_pss_encode($m, 8 * $this->k - 1);
        $m = $this->os2ip($em);
        $s = $this->rsasp1($m);
        $s = $this->i2osp($s, $this->k);
        return $s;
    }
    private function rsassa_pkcs1_v1_5_sign($m)
    {
        try {
            $em = $this->emsa_pkcs1_v1_5_encode($m, $this->k);
        } catch (LengthException $e) {
            throw new LengthException('RSA modulus too short');
        }
        $m = $this->os2ip($em);
        $s = $this->rsasp1($m);
        $s = $this->i2osp($s, $this->k);
        return $s;
    }
    public function sign($message)
    {
        switch ($this->signaturePadding) {
            case self::SIGNATURE_PKCS1:
            case self::SIGNATURE_RELAXED_PKCS1:
                return $this->rsassa_pkcs1_v1_5_sign($message);
            default:
                return $this->rsassa_pss_sign($message);
        }
    }
    private function rsaes_pkcs1_v1_5_decrypt($c)
    {
        if (strlen($c) != $this->k) {
            throw new LengthException('Ciphertext representative too long');
        }
        $c = $this->os2ip($c);
        $m = $this->rsadp($c);
        $em = $this->i2osp($m, $this->k);
        if (ord($em[0]) != 0 || ord($em[1]) > 2) {
            throw new RuntimeException('Decryption error');
        }
        $ps = substr($em, 2, strpos($em, chr(0), 2) - 2);
        $m = substr($em, strlen($ps) + 3);
        if (strlen($ps) < 8) {
            throw new RuntimeException('Decryption error');
        }
        return $m;
    }
    private function rsaes_oaep_decrypt($c)
    {
        if (strlen($c) != $this->k || $this->k < 2 * $this->hLen + 2) {
            throw new LengthException('Ciphertext representative too long');
        }
        $c = $this->os2ip($c);
        $m = $this->rsadp($c);
        $em = $this->i2osp($m, $this->k);
        $lHash = $this->hash->hash($this->label);
        $y = ord($em[0]);
        $maskedSeed = substr($em, 1, $this->hLen);
        $maskedDB = substr($em, $this->hLen + 1);
        $seedMask = $this->mgf1($maskedDB, $this->hLen);
        $seed = $maskedSeed ^ $seedMask;
        $dbMask = $this->mgf1($seed, $this->k - $this->hLen - 1);
        $db = $maskedDB ^ $dbMask;
        $lHash2 = substr($db, 0, $this->hLen);
        $m = substr($db, $this->hLen);
        $hashesMatch = hash_equals($lHash, $lHash2);
        $leadingZeros = 1;
        $patternMatch = 0;
        $offset = 0;
        for ($i = 0; $i < strlen($m); $i++) {
            $patternMatch |= $leadingZeros & $m[$i] === "\x01";
            $leadingZeros &= $m[$i] === "\x00";
            $offset += $patternMatch ? 0 : 1;
        }
        if (!$hashesMatch | !$patternMatch) {
            throw new RuntimeException('Decryption error');
        }
        return substr($m, $offset + 1);
    }
    private function raw_encrypt($m)
    {
        if (strlen($m) > $this->k) {
            throw new LengthException('Ciphertext representative too long');
        }
        $temp = $this->os2ip($m);
        $temp = $this->rsadp($temp);
        return $this->i2osp($temp, $this->k);
    }
    public function decrypt($ciphertext)
    {
        switch ($this->encryptionPadding) {
            case self::ENCRYPTION_NONE:
                return $this->raw_encrypt($ciphertext);
            case self::ENCRYPTION_PKCS1:
                return $this->rsaes_pkcs1_v1_5_decrypt($ciphertext);
            default:
                return $this->rsaes_oaep_decrypt($ciphertext);
        }
    }
    public function getPublicKey()
    {
        $type = self::validatePlugin('Keys', 'PKCS8', 'savePublicKey');
        if (empty($this->modulus) || empty($this->publicExponent)) {
            throw new RuntimeException('Public key components not found');
        }
        $key = $type::savePublicKey($this->modulus, $this->publicExponent);
        return RSA::loadFormat('PKCS8', $key)->withHash($this->hash->getHash())->withMGFHash($this->mgfHash->getHash())->withSaltLength($this->sLen)->withLabel($this->label)->withPadding($this->signaturePadding | $this->encryptionPadding);
    }
    /**
     * @param mixed[] $options
     */
    public function toString($type, $options = [])
    {
        $type = self::validatePlugin('Keys', $type, empty($this->primes) ? 'savePublicKey' : 'savePrivateKey');
        if ($type == PSS::class) {
            if ($this->signaturePadding == self::SIGNATURE_PSS) {
                $options += ['hash' => $this->hash->getHash(), 'MGFHash' => $this->mgfHash->getHash(), 'saltLength' => $this->getSaltLength()];
            } else {
                throw new UnsupportedFormatException('The PSS format can only be used when the signature method has been explicitly set to PSS');
            }
        }
        if (empty($this->primes)) {
            return $type::savePublicKey($this->modulus, $this->exponent, $options);
        }
        return $type::savePrivateKey($this->modulus, $this->publicExponent, $this->exponent, $this->primes, $this->exponents, $this->coefficients, $this->password, $options);
    }
}
