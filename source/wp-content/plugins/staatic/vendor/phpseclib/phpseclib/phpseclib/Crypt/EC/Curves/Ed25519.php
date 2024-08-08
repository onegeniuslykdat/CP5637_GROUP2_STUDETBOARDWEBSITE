<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Curves;

use RuntimeException;
use LengthException;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards;
use Staatic\Vendor\phpseclib3\Crypt\Hash;
use Staatic\Vendor\phpseclib3\Crypt\Random;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
class Ed25519 extends TwistedEdwards
{
    const HASH = 'sha512';
    const SIZE = 32;
    public function __construct()
    {
        $this->setModulo(new BigInteger('7FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFED', 16));
        $this->setCoefficients(new BigInteger('7FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEC', 16), new BigInteger('52036CEE2B6FFE738CC740797779E89800700A4D4141D8AB75EB4DCA135978A3', 16));
        $this->setBasePoint(new BigInteger('216936D3CD6E53FEC0A4E231FDD6DC5C692CC7609525A7B2C9562D608F25D51A', 16), new BigInteger('6666666666666666666666666666666666666666666666666666666666666658', 16));
        $this->setOrder(new BigInteger('1000000000000000000000000000000014DEF9DEA2F79CD65812631A5CF5D3ED', 16));
    }
    /**
     * @param BigInteger $y
     */
    public function recoverX($y, $sign)
    {
        $y = $this->factory->newInteger($y);
        $y2 = $y->multiply($y);
        $u = $y2->subtract($this->one);
        $v = $this->d->multiply($y2)->add($this->one);
        $x2 = $u->divide($v);
        if ($x2->equals($this->zero)) {
            if ($sign) {
                throw new RuntimeException('Unable to recover X coordinate (x2 = 0)');
            }
            return clone $this->zero;
        }
        $exp = $this->getModulo()->add(new BigInteger(3));
        $exp = $exp->bitwise_rightShift(3);
        $x = $x2->pow($exp);
        if (!$x->multiply($x)->subtract($x2)->equals($this->zero)) {
            $temp = $this->getModulo()->subtract(new BigInteger(1));
            $temp = $temp->bitwise_rightShift(2);
            $temp = $this->two->pow($temp);
            $x = $x->multiply($temp);
            if (!$x->multiply($x)->subtract($x2)->equals($this->zero)) {
                throw new RuntimeException('Unable to recover X coordinate');
            }
        }
        if ($x->isOdd() != $sign) {
            $x = $x->negate();
        }
        return [$x, $y];
    }
    public function extractSecret($str)
    {
        if (strlen($str) != 32) {
            throw new LengthException('Private Key should be 32-bytes long');
        }
        $hash = new Hash('sha512');
        $h = $hash->hash($str);
        $h = substr($h, 0, 32);
        $h[0] = $h[0] & chr(0xf8);
        $h = strrev($h);
        $h[0] = $h[0] & chr(0x3f) | chr(0x40);
        $dA = new BigInteger($h, 256);
        return ['dA' => $dA, 'secret' => $str];
    }
    public function encodePoint($point)
    {
        list($x, $y) = $point;
        $y = $y->toBytes();
        $y[0] = $y[0] & chr(0x7f);
        if ($x->isOdd()) {
            $y[0] = $y[0] | chr(0x80);
        }
        $y = strrev($y);
        return $y;
    }
    public function createRandomMultiplier()
    {
        return $this->extractSecret(Random::string(32))['dA'];
    }
    /**
     * @param mixed[] $p
     */
    public function convertToInternal($p)
    {
        if (empty($p)) {
            return [clone $this->zero, clone $this->one, clone $this->one, clone $this->zero];
        }
        if (isset($p[2])) {
            return $p;
        }
        $p[2] = clone $this->one;
        $p[3] = $p[0]->multiply($p[1]);
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
        list($x1, $y1, $z1, $t1) = $p;
        $a = $x1->multiply($x1);
        $b = $y1->multiply($y1);
        $c = $this->two->multiply($z1)->multiply($z1);
        $h = $a->add($b);
        $temp = $x1->add($y1);
        $e = $h->subtract($temp->multiply($temp));
        $g = $a->subtract($b);
        $f = $c->add($g);
        $x3 = $e->multiply($f);
        $y3 = $g->multiply($h);
        $t3 = $e->multiply($h);
        $z3 = $f->multiply($g);
        return [$x3, $y3, $z3, $t3];
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
        list($x1, $y1, $z1, $t1) = $p;
        list($x2, $y2, $z2, $t2) = $q;
        $a = $y1->subtract($x1)->multiply($y2->subtract($x2));
        $b = $y1->add($x1)->multiply($y2->add($x2));
        $c = $t1->multiply($this->two)->multiply($this->d)->multiply($t2);
        $d = $z1->multiply($this->two)->multiply($z2);
        $e = $b->subtract($a);
        $f = $d->subtract($c);
        $g = $d->add($c);
        $h = $b->add($a);
        $x3 = $e->multiply($f);
        $y3 = $g->multiply($h);
        $t3 = $e->multiply($h);
        $z3 = $f->multiply($g);
        return [$x3, $y3, $z3, $t3];
    }
}
