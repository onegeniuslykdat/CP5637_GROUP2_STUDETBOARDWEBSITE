<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves;

use RuntimeException;
use UnexpectedValueException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
use Staatic\Vendor\phpseclib3\Math\BinaryField;
use Staatic\Vendor\phpseclib3\Math\BinaryField\Integer as BinaryInteger;
class Binary extends Base
{
    protected $factory;
    protected $a;
    protected $b;
    protected $p;
    protected $one;
    protected $modulo;
    protected $order;
    public function setModulo(...$modulo)
    {
        $this->modulo = $modulo;
        $this->factory = new BinaryField(...$modulo);
        $this->one = $this->factory->newInteger("\x01");
    }
    public function setCoefficients($a, $b)
    {
        if (!isset($this->factory)) {
            throw new RuntimeException('setModulo needs to be called before this method');
        }
        $this->a = $this->factory->newInteger(pack('H*', $a));
        $this->b = $this->factory->newInteger(pack('H*', $b));
    }
    public function setBasePoint($x, $y)
    {
        switch (\true) {
            case !is_string($x) && !$x instanceof BinaryInteger:
                throw new UnexpectedValueException('Argument 1 passed to Binary::setBasePoint() must be a string or an instance of BinaryField\Integer');
            case !is_string($y) && !$y instanceof BinaryInteger:
                throw new UnexpectedValueException('Argument 2 passed to Binary::setBasePoint() must be a string or an instance of BinaryField\Integer');
        }
        if (!isset($this->factory)) {
            throw new RuntimeException('setModulo needs to be called before this method');
        }
        $this->p = [is_string($x) ? $this->factory->newInteger(pack('H*', $x)) : $x, is_string($y) ? $this->factory->newInteger(pack('H*', $y)) : $y];
    }
    public function getBasePoint()
    {
        if (!isset($this->factory)) {
            throw new RuntimeException('setModulo needs to be called before this method');
        }
        return $this->p;
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
        $o1 = $z1->multiply($z1);
        $b = $x2->multiply($o1);
        if ($z2->equals($this->one)) {
            $d = $y2->multiply($o1)->multiply($z1);
            $e = $x1->add($b);
            $f = $y1->add($d);
            $z3 = $e->multiply($z1);
            $h = $f->multiply($x2)->add($z3->multiply($y2));
            $i = $f->add($z3);
            $g = $z3->multiply($z3);
            $p1 = $this->a->multiply($g);
            $p2 = $f->multiply($i);
            $p3 = $e->multiply($e)->multiply($e);
            $x3 = $p1->add($p2)->add($p3);
            $y3 = $i->multiply($x3)->add($g->multiply($h));
            return [$x3, $y3, $z3];
        }
        $o2 = $z2->multiply($z2);
        $a = $x1->multiply($o2);
        $c = $y1->multiply($o2)->multiply($z2);
        $d = $y2->multiply($o1)->multiply($z1);
        $e = $a->add($b);
        $f = $c->add($d);
        $g = $e->multiply($z1);
        $h = $f->multiply($x2)->add($g->multiply($y2));
        $z3 = $g->multiply($z2);
        $i = $f->add($z3);
        $p1 = $this->a->multiply($z3->multiply($z3));
        $p2 = $f->multiply($i);
        $p3 = $e->multiply($e)->multiply($e);
        $x3 = $p1->add($p2)->add($p3);
        $y3 = $i->multiply($x3)->add($g->multiply($g)->multiply($h));
        return [$x3, $y3, $z3];
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
        $a = $x1->multiply($x1);
        $b = $a->multiply($a);
        if ($z1->equals($this->one)) {
            $x3 = $b->add($this->b);
            $z3 = clone $x1;
            $p1 = $a->add($y1)->add($z3)->multiply($this->b);
            $p2 = $a->add($y1)->multiply($b);
            $y3 = $p1->add($p2);
            return [$x3, $y3, $z3];
        }
        $c = $z1->multiply($z1);
        $d = $c->multiply($c);
        $x3 = $b->add($this->b->multiply($d->multiply($d)));
        $z3 = $x1->multiply($c);
        $p1 = $b->multiply($z3);
        $p2 = $a->add($y1->multiply($z1))->add($z3)->multiply($x3);
        $y3 = $p1->add($p2);
        return [$x3, $y3, $z3];
    }
    public function derivePoint($m)
    {
        throw new RuntimeException('Point compression on binary finite field elliptic curves is not supported');
    }
    /**
     * @param mixed[] $p
     */
    public function verifyPoint($p)
    {
        list($x, $y) = $p;
        $lhs = $y->multiply($y);
        $lhs = $lhs->add($x->multiply($y));
        $x2 = $x->multiply($x);
        $x3 = $x2->multiply($x);
        $rhs = $x3->add($this->a->multiply($x2))->add($this->b);
        return $lhs->equals($rhs);
    }
    public function getModulo()
    {
        return $this->modulo;
    }
    public function getA()
    {
        return $this->a;
    }
    public function getB()
    {
        return $this->b;
    }
    /**
     * @param mixed[] $p
     */
    public function convertToAffine($p)
    {
        if (!isset($p[2])) {
            return $p;
        }
        list($x, $y, $z) = $p;
        $z = $this->one->divide($z);
        $z2 = $z->multiply($z);
        return [$x->multiply($z2), $y->multiply($z2)->multiply($z)];
    }
    /**
     * @param mixed[] $p
     */
    public function convertToInternal($p)
    {
        if (isset($p[2])) {
            return $p;
        }
        $p[2] = clone $this->one;
        $p['fresh'] = \true;
        return $p;
    }
}
