<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use RuntimeException;
use LengthException;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards;
use Staatic\Vendor\phpseclib3\Crypt\Hash;
use Staatic\Vendor\phpseclib3\Crypt\Random;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class Ed448 extends TwistedEdwards
{
    const HASH = 'shake256-912';
    const SIZE = 57;
    public function __construct()
    {
        $this->setModulo(new BigInteger('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFE' . 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF', 16));
        $this->setCoefficients(new BigInteger(1), new BigInteger('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFE' . 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF6756', 16));
        $this->setBasePoint(new BigInteger('4F1970C66BED0DED221D15A622BF36DA9E146570470F1767EA6DE324' . 'A3D3A46412AE1AF72AB66511433B80E18B00938E2626A82BC70CC05E', 16), new BigInteger('693F46716EB6BC248876203756C9C7624BEA73736CA3984087789C1E' . '05A0C2D73AD3FF1CE67C39C4FDBD132C4ED7C8AD9808795BF230FA14', 16));
        $this->setOrder(new BigInteger('3FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF' . '7CCA23E9C44EDB49AED63690216CC2728DC58F552378C292AB5844F3', 16));
    }
    /**
     * @param BigInteger $y
     */
    public function recoverX($y, $sign)
    {
        $y = $this->factory->newInteger($y);
        $y2 = $y->multiply($y);
        $u = $y2->subtract($this->one);
        $v = $this->d->multiply($y2)->subtract($this->one);
        $x2 = $u->divide($v);
        if ($x2->equals($this->zero)) {
            if ($sign) {
                throw new RuntimeException('Unable to recover X coordinate (x2 = 0)');
            }
            return clone $this->zero;
        }
        $exp = $this->getModulo()->add(new BigInteger(1));
        $exp = $exp->bitwise_rightShift(2);
        $x = $x2->pow($exp);
        if (!$x->multiply($x)->subtract($x2)->equals($this->zero)) {
            throw new RuntimeException('Unable to recover X coordinate');
        }
        if ($x->isOdd() != $sign) {
            $x = $x->negate();
        }
        return [$x, $y];
    }
    public function extractSecret($str)
    {
        if (strlen($str) != 57) {
            throw new LengthException('Private Key should be 57-bytes long');
        }
        $hash = new Hash('shake256-912');
        $h = $hash->hash($str);
        $h = substr($h, 0, 57);
        $h[0] = $h[0] & chr(0xfc);
        $h = strrev($h);
        $h[0] = "\x00";
        $h[1] = $h[1] | chr(0x80);
        $dA = new BigInteger($h, 256);
        return ['dA' => $dA, 'secret' => $str];
        $dA->secret = $str;
        return $dA;
    }
    public function encodePoint($point)
    {
        list($x, $y) = $point;
        $y = "\x00" . $y->toBytes();
        if ($x->isOdd()) {
            $y[0] = $y[0] | chr(0x80);
        }
        $y = strrev($y);
        return $y;
    }
    public function createRandomMultiplier()
    {
        return $this->extractSecret(Random::string(57))['dA'];
    }
    /**
     * @param mixed[] $p
     */
    public function convertToInternal($p)
    {
        if (empty($p)) {
            return [clone $this->zero, clone $this->one, clone $this->one];
        }
        if (isset($p[2])) {
            return $p;
        }
        $p[2] = clone $this->one;
        return $p;
    }
    /**
     * @param mixed[] $p
     */
    public function doublePoint($p)
    {
        if (!isset($this->factory)) {
            throw new RuntimeException('setModulo needs to be called before this method');
        }
        if (!count($p)) {
            return [];
        }
        if (!isset($p[2])) {
            throw new RuntimeException('Affine coordinates need to be manually converted to "Jacobi" coordinates or vice versa');
        }
        list($x1, $y1, $z1) = $p;
        $b = $x1->add($y1);
        $b = $b->multiply($b);
        $c = $x1->multiply($x1);
        $d = $y1->multiply($y1);
        $e = $c->add($d);
        $h = $z1->multiply($z1);
        $j = $e->subtract($this->two->multiply($h));
        $x3 = $b->subtract($e)->multiply($j);
        $y3 = $c->subtract($d)->multiply($e);
        $z3 = $e->multiply($j);
        return [$x3, $y3, $z3];
    }
    /**
     * @param mixed[] $p
     * @param mixed[] $q
     */
    public function addPoint($p, $q)
    {
        if (!isset($this->factory)) {
            throw new RuntimeException('setModulo needs to be called before this method');
        }
        if (!count($p) || !count($q)) {
            if (count($q)) {
                return $q;
            }
            if (count($p)) {
                return $p;
            }
            return [];
        }
        if (!isset($p[2]) || !isset($q[2])) {
            throw new RuntimeException('Affine coordinates need to be manually converted to "Jacobi" coordinates or vice versa');
        }
        if ($p[0]->equals($q[0])) {
            return (!$p[1]->equals($q[1])) ? [] : $this->doublePoint($p);
        }
        list($x1, $y1, $z1) = $p;
        list($x2, $y2, $z2) = $q;
        $a = $z1->multiply($z2);
        $b = $a->multiply($a);
        $c = $x1->multiply($x2);
        $d = $y1->multiply($y2);
        $e = $this->d->multiply($c)->multiply($d);
        $f = $b->subtract($e);
        $g = $b->add($e);
        $h = $x1->add($y1)->multiply($x2->add($y2));
        $x3 = $a->multiply($f)->multiply($h->subtract($c)->subtract($d));
        $y3 = $a->multiply($g)->multiply($d->subtract($c));
        $z3 = $f->multiply($g);
        return [$x3, $y3, $z3];
    }
}
