<?php

namespace Staatic\Vendor\phpseclib3\Crypt\DSA;

use Staatic\Vendor\phpseclib3\Crypt\Common\Traits\PasswordProtected;
use Staatic\Vendor\phpseclib3\Crypt\Common;
use Staatic\Vendor\phpseclib3\Crypt\DSA;
use Staatic\Vendor\phpseclib3\Crypt\DSA\Formats\Signature\ASN1 as ASN1Signature;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
final class PrivateKey extends DSA implements Common\PrivateKey
{
    use PasswordProtected;
    protected $x;
    public function getPublicKey()
    {
        $type = self::validatePlugin('Keys', 'PKCS8', 'savePublicKey');
        if (!isset($this->y)) {
            $this->y = $this->g->powMod($this->x, $this->p);
        }
        $key = $type::savePublicKey($this->p, $this->q, $this->g, $this->y);
        return DSA::loadFormat('PKCS8', $key)->withHash($this->hash->getHash())->withSignatureFormat($this->shortFormat);
    }
    public function sign($message)
    {
        $format = $this->sigFormat;
        if (self::$engines['OpenSSL'] && in_array($this->hash->getHash(), openssl_get_md_methods())) {
            $signature = '';
            $result = openssl_sign($message, $signature, $this->toString('PKCS8'), $this->hash->getHash());
            if ($result) {
                if ($this->shortFormat == 'ASN1') {
                    return $signature;
                }
                extract(ASN1Signature::load($signature));
                return $format::save($r, $s);
            }
        }
        $h = $this->hash->hash($message);
        $h = $this->bits2int($h);
        while (\true) {
            $k = BigInteger::randomRange(self::$one, $this->q->subtract(self::$one));
            $r = $this->g->powMod($k, $this->p);
            list(, $r) = $r->divide($this->q);
            if ($r->equals(self::$zero)) {
                continue;
            }
            $kinv = $k->modInverse($this->q);
            $temp = $h->add($this->x->multiply($r));
            $temp = $kinv->multiply($temp);
            list(, $s) = $temp->divide($this->q);
            if (!$s->equals(self::$zero)) {
                break;
            }
        }
        return $format::save($r, $s);
    }
    /**
     * @param mixed[] $options
     */
    public function toString($type, $options = [])
    {
        $type = self::validatePlugin('Keys', $type, 'savePrivateKey');
        if (!isset($this->y)) {
            $this->y = $this->g->powMod($this->x, $this->p);
        }
        return $type::savePrivateKey($this->p, $this->q, $this->g, $this->y, $this->x, $this->password, $options);
    }
}
