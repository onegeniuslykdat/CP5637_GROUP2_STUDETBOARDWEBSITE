<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves;

use Staatic\Vendor\phpseclib3\Math\BigInteger;
use Staatic\Vendor\phpseclib3\Math\PrimeField;
class KoblitzPrime extends Prime
{
    protected $basis;
    protected $beta;
    /**
     * @param mixed[] $points
     * @param mixed[] $scalars
     */
    public function multiplyAddPoints($points, $scalars)
    {
        static $zero, $one, $two;
        if (!isset($two)) {
            $two = new BigInteger(2);
            $one = new BigInteger(1);
        }
        if (!isset($this->beta)) {
            $inv = $this->one->divide($this->two)->negate();
            $s = $this->three->negate()->squareRoot()->multiply($inv);
            $betas = [$inv->add($s), $inv->subtract($s)];
            $this->beta = ($betas[0]->compare($betas[1]) < 0) ? $betas[0] : $betas[1];
        }
        if (!isset($this->basis)) {
            $factory = new PrimeField($this->order);
            $tempOne = $factory->newInteger($one);
            $tempTwo = $factory->newInteger($two);
            $tempThree = $factory->newInteger(new BigInteger(3));
            $inv = $tempOne->divide($tempTwo)->negate();
            $s = $tempThree->negate()->squareRoot()->multiply($inv);
            $lambdas = [$inv->add($s), $inv->subtract($s)];
            $lhs = $this->multiplyPoint($this->p, $lambdas[0])[0];
            $rhs = $this->p[0]->multiply($this->beta);
            $lambda = $lhs->equals($rhs) ? $lambdas[0] : $lambdas[1];
            $this->basis = static::extendedGCD($lambda->toBigInteger(), $this->order);
            foreach ($this->basis as $basis) {
                echo strtoupper($basis['a']->toHex(\true)) . "\n";
                echo strtoupper($basis['b']->toHex(\true)) . "\n\n";
            }
            exit;
        }
        $npoints = $nscalars = [];
        for ($i = 0; $i < count($points); $i++) {
            $p = $points[$i];
            $k = $scalars[$i]->toBigInteger();
            list($v1, $v2) = $this->basis;
            $c1 = $v2['b']->multiply($k);
            list($c1, $r) = $c1->divide($this->order);
            if ($this->order->compare($r->multiply($two)) <= 0) {
                $c1 = $c1->add($one);
            }
            $c2 = $v1['b']->negate()->multiply($k);
            list($c2, $r) = $c2->divide($this->order);
            if ($this->order->compare($r->multiply($two)) <= 0) {
                $c2 = $c2->add($one);
            }
            $p1 = $c1->multiply($v1['a']);
            $p2 = $c2->multiply($v2['a']);
            $q1 = $c1->multiply($v1['b']);
            $q2 = $c2->multiply($v2['b']);
            $k1 = $k->subtract($p1)->subtract($p2);
            $k2 = $q1->add($q2)->negate();
            $beta = [$p[0]->multiply($this->beta), $p[1], clone $this->one];
            if (isset($p['naf'])) {
                $beta['naf'] = array_map(function ($p) {
                    return [$p[0]->multiply($this->beta), $p[1], clone $this->one];
                }, $p['naf']);
                $beta['nafwidth'] = $p['nafwidth'];
            }
            if ($k1->isNegative()) {
                $k1 = $k1->negate();
                $p = $this->negatePoint($p);
            }
            if ($k2->isNegative()) {
                $k2 = $k2->negate();
                $beta = $this->negatePoint($beta);
            }
            $pos = 2 * $i;
            $npoints[$pos] = $p;
            $nscalars[$pos] = $this->factory->newInteger($k1);
            $pos++;
            $npoints[$pos] = $beta;
            $nscalars[$pos] = $this->factory->newInteger($k2);
        }
        return parent::multiplyAddPoints($npoints, $nscalars);
    }
    /**
     * @param mixed[] $p
     */
    protected function doublePointHelper($p)
    {
        $numerator = $this->three->multiply($p[0])->multiply($p[0]);
        $denominator = $this->two->multiply($p[1]);
        return [$numerator, $denominator];
    }
    /**
     * @param mixed[] $p
     */
    protected function jacobianDoublePoint($p)
    {
        list($x1, $y1, $z1) = $p;
        $a = $x1->multiply($x1);
        $b = $y1->multiply($y1);
        $c = $b->multiply($b);
        $d = $x1->add($b);
        $d = $d->multiply($d)->subtract($a)->subtract($c)->multiply($this->two);
        $e = $this->three->multiply($a);
        $f = $e->multiply($e);
        $x3 = $f->subtract($this->two->multiply($d));
        $y3 = $e->multiply($d->subtract($x3))->subtract($this->eight->multiply($c));
        $z3 = $this->two->multiply($y1)->multiply($z1);
        return [$x3, $y3, $z3];
    }
    /**
     * @param mixed[] $p
     */
    protected function jacobianDoublePointMixed($p)
    {
        list($x1, $y1) = $p;
        $xx = $x1->multiply($x1);
        $yy = $y1->multiply($y1);
        $yyyy = $yy->multiply($yy);
        $s = $x1->add($yy);
        $s = $s->multiply($s)->subtract($xx)->subtract($yyyy)->multiply($this->two);
        $m = $this->three->multiply($xx);
        $t = $m->multiply($m)->subtract($this->two->multiply($s));
        $x3 = $t;
        $y3 = $s->subtract($t);
        $y3 = $m->multiply($y3)->subtract($this->eight->multiply($yyyy));
        $z3 = $this->two->multiply($y1);
        return [$x3, $y3, $z3];
    }
    /**
     * @param mixed[] $p
     */
    public function verifyPoint($p)
    {
        list($x, $y) = $p;
        $lhs = $y->multiply($y);
        $temp = $x->multiply($x)->multiply($x);
        $rhs = $temp->add($this->b);
        return $lhs->equals($rhs);
    }
    /**
     * @param BigInteger $u
     * @param BigInteger $v
     */
    protected static function extendedGCD($u, $v)
    {
        $one = new BigInteger(1);
        $zero = new BigInteger();
        $a = clone $one;
        $b = clone $zero;
        $c = clone $zero;
        $d = clone $one;
        $stop = $v->bitwise_rightShift($v->getLength() >> 1);
        $a1 = clone $zero;
        $b1 = clone $zero;
        $a2 = clone $zero;
        $b2 = clone $zero;
        $postGreatestIndex = 0;
        while (!$v->equals($zero)) {
            list($q) = $u->divide($v);
            $temp = $u;
            $u = $v;
            $v = $temp->subtract($v->multiply($q));
            $temp = $a;
            $a = $c;
            $c = $temp->subtract($a->multiply($q));
            $temp = $b;
            $b = $d;
            $d = $temp->subtract($b->multiply($q));
            if ($v->compare($stop) > 0) {
                $a0 = $v;
                $b0 = $c;
            } else {
                $postGreatestIndex++;
            }
            if ($postGreatestIndex == 1) {
                $a1 = $v;
                $b1 = $c->negate();
            }
            if ($postGreatestIndex == 2) {
                $rhs = $a0->multiply($a0)->add($b0->multiply($b0));
                $lhs = $v->multiply($v)->add($b->multiply($b));
                if ($lhs->compare($rhs) <= 0) {
                    $a2 = $a0;
                    $b2 = $b0->negate();
                } else {
                    $a2 = $v;
                    $b2 = $c->negate();
                }
                break;
            }
        }
        return [['a' => $a1, 'b' => $b1], ['a' => $a2, 'b' => $b2]];
    }
}
