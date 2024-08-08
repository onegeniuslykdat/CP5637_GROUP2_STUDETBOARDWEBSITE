<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves;

use RuntimeException;
use UnexpectedValueException;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
use Staatic\Vendor\phpseclib3\Math\Common\FiniteField\Integer;
use Staatic\Vendor\phpseclib3\Math\PrimeField;
use Staatic\Vendor\phpseclib3\Math\PrimeField\Integer as PrimeInteger;
class Prime extends Base
{
    protected $factory;
    protected $a;
    protected $b;
    protected $p;
    protected $one;
    protected $two;
    protected $three;
    protected $four;
    protected $eight;
    protected $modulo;
    protected $order;
    /**
     * @param BigInteger $modulo
     */
    public function setModulo($modulo)
    {
        $this->modulo = $modulo;
        $this->factory = new PrimeField($modulo);
        $this->two = $this->factory->newInteger(new BigInteger(2));
        $this->three = $this->factory->newInteger(new BigInteger(3));
        $this->one = $this->factory->newInteger(new BigInteger(1));
        $this->four = $this->factory->newInteger(new BigInteger(4));
        $this->eight = $this->factory->newInteger(new BigInteger(8));
    }
    /**
     * @param BigInteger $a
     * @param BigInteger $b
     */
    public function setCoefficients($a, $b)
    {
        if (!isset($this->factory)) {
            throw new RuntimeException('setModulo needs to be called before this method');
        }
        $this->a = $this->factory->newInteger($a);
        $this->b = $this->factory->newInteger($b);
    }
    public function setBasePoint($x, $y)
    {
        switch (\true) {
            case !$x instanceof BigInteger && !$x instanceof PrimeInteger:
                throw new UnexpectedValueException('Argument 1 passed to Prime::setBasePoint() must be an instance of either BigInteger or PrimeField\Integer');
            case !$y instanceof BigInteger && !$y instanceof PrimeInteger:
                throw new UnexpectedValueException('Argument 2 passed to Prime::setBasePoint() must be an instance of either BigInteger or PrimeField\Integer');
        }
        if (!isset($this->factory)) {
            throw new RuntimeException('setModulo needs to be called before this method');
        }
        $this->p = [($x instanceof BigInteger) ? $this->factory->newInteger($x) : $x, ($y instanceof BigInteger) ? $this->factory->newInteger($y) : $y];
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
    protected function jacobianAddPointMixedXY($p, $q)
    {
        list($u1, $s1) = $p;
        list($u2, $s2) = $q;
        if ($u1->equals($u2)) {
            if (!$s1->equals($s2)) {
                return [];
            } else {
                return $this->doublePoint($p);
            }
        }
        $h = $u2->subtract($u1);
        $r = $s2->subtract($s1);
        $h2 = $h->multiply($h);
        $h3 = $h2->multiply($h);
        $v = $u1->multiply($h2);
        $x3 = $r->multiply($r)->subtract($h3)->subtract($v->multiply($this->two));
        $y3 = $r->multiply($v->subtract($x3))->subtract($s1->multiply($h3));
        return [$x3, $y3, $h];
    }
    /**
     * @param mixed[] $p
     * @param mixed[] $q
     */
    protected function jacobianAddPointMixedX($p, $q)
    {
        list($u1, $s1, $z1) = $p;
        list($x2, $y2) = $q;
        $z12 = $z1->multiply($z1);
        $u2 = $x2->multiply($z12);
        $s2 = $y2->multiply($z12->multiply($z1));
        if ($u1->equals($u2)) {
            if (!$s1->equals($s2)) {
                return [];
            } else {
                return $this->doublePoint($p);
            }
        }
        $h = $u2->subtract($u1);
        $r = $s2->subtract($s1);
        $h2 = $h->multiply($h);
        $h3 = $h2->multiply($h);
        $v = $u1->multiply($h2);
        $x3 = $r->multiply($r)->subtract($h3)->subtract($v->multiply($this->two));
        $y3 = $r->multiply($v->subtract($x3))->subtract($s1->multiply($h3));
        $z3 = $h->multiply($z1);
        return [$x3, $y3, $z3];
    }
    /**
     * @param mixed[] $p
     * @param mixed[] $q
     */
    protected function jacobianAddPoint($p, $q)
    {
        list($x1, $y1, $z1) = $p;
        list($x2, $y2, $z2) = $q;
        $z12 = $z1->multiply($z1);
        $z22 = $z2->multiply($z2);
        $u1 = $x1->multiply($z22);
        $u2 = $x2->multiply($z12);
        $s1 = $y1->multiply($z22->multiply($z2));
        $s2 = $y2->multiply($z12->multiply($z1));
        if ($u1->equals($u2)) {
            if (!$s1->equals($s2)) {
                return [];
            } else {
                return $this->doublePoint($p);
            }
        }
        $h = $u2->subtract($u1);
        $r = $s2->subtract($s1);
        $h2 = $h->multiply($h);
        $h3 = $h2->multiply($h);
        $v = $u1->multiply($h2);
        $x3 = $r->multiply($r)->subtract($h3)->subtract($v->multiply($this->two));
        $y3 = $r->multiply($v->subtract($x3))->subtract($s1->multiply($h3));
        $z3 = $h->multiply($z1)->multiply($z2);
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
        if (isset($p[2]) && isset($q[2])) {
            if (isset($p['fresh']) && isset($q['fresh'])) {
                return $this->jacobianAddPointMixedXY($p, $q);
            }
            if (isset($p['fresh'])) {
                return $this->jacobianAddPointMixedX($q, $p);
            }
            if (isset($q['fresh'])) {
                return $this->jacobianAddPointMixedX($p, $q);
            }
            return $this->jacobianAddPoint($p, $q);
        }
        if (isset($p[2]) || isset($q[2])) {
            throw new RuntimeException('Affine coordinates need to be manually converted to Jacobi coordinates or vice versa');
        }
        if ($p[0]->equals($q[0])) {
            if (!$p[1]->equals($q[1])) {
                return [];
            } else {
                list($numerator, $denominator) = $this->doublePointHelper($p);
            }
        } else {
            $numerator = $q[1]->subtract($p[1]);
            $denominator = $q[0]->subtract($p[0]);
        }
        $slope = $numerator->divide($denominator);
        $x = $slope->multiply($slope)->subtract($p[0])->subtract($q[0]);
        $y = $slope->multiply($p[0]->subtract($x))->subtract($p[1]);
        return [$x, $y];
    }
    /**
     * @param mixed[] $p
     */
    protected function doublePointHelper($p)
    {
        $numerator = $this->three->multiply($p[0])->multiply($p[0])->add($this->a);
        $denominator = $this->two->multiply($p[1]);
        return [$numerator, $denominator];
    }
    /**
     * @param mixed[] $p
     */
    protected function jacobianDoublePoint($p)
    {
        list($x, $y, $z) = $p;
        $x2 = $x->multiply($x);
        $y2 = $y->multiply($y);
        $z2 = $z->multiply($z);
        $s = $this->four->multiply($x)->multiply($y2);
        $m1 = $this->three->multiply($x2);
        $m2 = $this->a->multiply($z2->multiply($z2));
        $m = $m1->add($m2);
        $x1 = $m->multiply($m)->subtract($this->two->multiply($s));
        $y1 = $m->multiply($s->subtract($x1))->subtract($this->eight->multiply($y2->multiply($y2)));
        $z1 = $this->two->multiply($y)->multiply($z);
        return [$x1, $y1, $z1];
    }
    /**
     * @param mixed[] $p
     */
    protected function jacobianDoublePointMixed($p)
    {
        list($x, $y) = $p;
        $x2 = $x->multiply($x);
        $y2 = $y->multiply($y);
        $s = $this->four->multiply($x)->multiply($y2);
        $m1 = $this->three->multiply($x2);
        $m = $m1->add($this->a);
        $x1 = $m->multiply($m)->subtract($this->two->multiply($s));
        $y1 = $m->multiply($s->subtract($x1))->subtract($this->eight->multiply($y2->multiply($y2)));
        $z1 = $this->two->multiply($y);
        return [$x1, $y1, $z1];
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
        if (isset($p[2])) {
            if (isset($p['fresh'])) {
                return $this->jacobianDoublePointMixed($p);
            }
            return $this->jacobianDoublePoint($p);
        }
        list($numerator, $denominator) = $this->doublePointHelper($p);
        $slope = $numerator->divide($denominator);
        $x = $slope->multiply($slope)->subtract($p[0])->subtract($p[0]);
        $y = $slope->multiply($p[0]->subtract($x))->subtract($p[1]);
        return [$x, $y];
    }
    public function derivePoint($m)
    {
        $y = ord(Strings::shift($m));
        $x = new BigInteger($m, 256);
        $xp = $this->convertInteger($x);
        switch ($y) {
            case 2:
                $ypn = \false;
                break;
            case 3:
                $ypn = \true;
                break;
            default:
                throw new RuntimeException('Coordinate not in recognized format');
        }
        $temp = $xp->multiply($this->a);
        $temp = $xp->multiply($xp)->multiply($xp)->add($temp);
        $temp = $temp->add($this->b);
        $b = $temp->squareRoot();
        if (!$b) {
            throw new RuntimeException('Unable to derive Y coordinate');
        }
        $bn = $b->isOdd();
        $yp = ($ypn == $bn) ? $b : $b->negate();
        return [$xp, $yp];
    }
    /**
     * @param mixed[] $p
     */
    public function verifyPoint($p)
    {
        list($x, $y) = $p;
        $lhs = $y->multiply($y);
        $temp = $x->multiply($this->a);
        $temp = $x->multiply($x)->multiply($x)->add($temp);
        $rhs = $temp->add($this->b);
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
     * @param mixed[] $points
     * @param mixed[] $scalars
     */
    public function multiplyAddPoints($points, $scalars)
    {
        $length = count($points);
        foreach ($points as &$point) {
            $point = $this->convertToInternal($point);
        }
        $wnd = [$this->getNAFPoints($points[0], 7)];
        $wndWidth = [isset($points[0]['nafwidth']) ? $points[0]['nafwidth'] : 7];
        for ($i = 1; $i < $length; $i++) {
            $wnd[] = $this->getNAFPoints($points[$i], 1);
            $wndWidth[] = isset($points[$i]['nafwidth']) ? $points[$i]['nafwidth'] : 1;
        }
        $naf = [];
        $max = 0;
        for ($i = $length - 1; $i >= 1; $i -= 2) {
            $a = $i - 1;
            $b = $i;
            if ($wndWidth[$a] != 1 || $wndWidth[$b] != 1) {
                $naf[$a] = $scalars[$a]->getNAF($wndWidth[$a]);
                $naf[$b] = $scalars[$b]->getNAF($wndWidth[$b]);
                $max = max(count($naf[$a]), count($naf[$b]), $max);
                continue;
            }
            $comb = [$points[$a], null, null, $points[$b]];
            $comb[1] = $this->addPoint($points[$a], $points[$b]);
            $comb[2] = $this->addPoint($points[$a], $this->negatePoint($points[$b]));
            $index = [-3, -1, -5, -7, 0, 7, 5, 1, 3];
            $jsf = self::getJSFPoints($scalars[$a], $scalars[$b]);
            $max = max(count($jsf[0]), $max);
            if ($max > 0) {
                $naf[$a] = array_fill(0, $max, 0);
                $naf[$b] = array_fill(0, $max, 0);
            } else {
                $naf[$a] = [];
                $naf[$b] = [];
            }
            for ($j = 0; $j < $max; $j++) {
                $ja = isset($jsf[0][$j]) ? $jsf[0][$j] : 0;
                $jb = isset($jsf[1][$j]) ? $jsf[1][$j] : 0;
                $naf[$a][$j] = $index[3 * ($ja + 1) + $jb + 1];
                $naf[$b][$j] = 0;
                $wnd[$a] = $comb;
            }
        }
        $acc = [];
        $temp = [0, 0, 0, 0];
        for ($i = $max; $i >= 0; $i--) {
            $k = 0;
            while ($i >= 0) {
                $zero = \true;
                for ($j = 0; $j < $length; $j++) {
                    $temp[$j] = isset($naf[$j][$i]) ? $naf[$j][$i] : 0;
                    if ($temp[$j] != 0) {
                        $zero = \false;
                    }
                }
                if (!$zero) {
                    break;
                }
                $k++;
                $i--;
            }
            if ($i >= 0) {
                $k++;
            }
            while ($k--) {
                $acc = $this->doublePoint($acc);
            }
            if ($i < 0) {
                break;
            }
            for ($j = 0; $j < $length; $j++) {
                $z = $temp[$j];
                $p = null;
                if ($z == 0) {
                    continue;
                }
                $p = ($z > 0) ? $wnd[$j][$z - 1 >> 1] : $this->negatePoint($wnd[$j][-$z - 1 >> 1]);
                $acc = $this->addPoint($acc, $p);
            }
        }
        return $this->convertToAffine($acc);
    }
    private function getNAFPoints(array $point, $wnd)
    {
        if (isset($point['naf'])) {
            return $point['naf'];
        }
        $res = [$point];
        $max = (1 << $wnd) - 1;
        $dbl = ($max == 1) ? null : $this->doublePoint($point);
        for ($i = 1; $i < $max; $i++) {
            $res[] = $this->addPoint($res[$i - 1], $dbl);
        }
        $point['naf'] = $res;
        return $res;
    }
    /**
     * @param mixed $k1
     * @param mixed $k2
     */
    private static function getJSFPoints($k1, $k2)
    {
        static $three;
        if (!isset($three)) {
            $three = new BigInteger(3);
        }
        $jsf = [[], []];
        $k1 = $k1->toBigInteger();
        $k2 = $k2->toBigInteger();
        $d1 = 0;
        $d2 = 0;
        while ($k1->compare(new BigInteger(-$d1)) > 0 || $k2->compare(new BigInteger(-$d2)) > 0) {
            $m14 = $k1->testBit(0) + 2 * $k1->testBit(1);
            $m14 += $d1;
            $m14 &= 3;
            $m24 = $k2->testBit(0) + 2 * $k2->testBit(1);
            $m24 += $d2;
            $m24 &= 3;
            if ($m14 == 3) {
                $m14 = -1;
            }
            if ($m24 == 3) {
                $m24 = -1;
            }
            $u1 = 0;
            if ($m14 & 1) {
                $m8 = $k1->testBit(0) + 2 * $k1->testBit(1) + 4 * $k1->testBit(2);
                $m8 += $d1;
                $m8 &= 7;
                $u1 = (($m8 == 3 || $m8 == 5) && $m24 == 2) ? -$m14 : $m14;
            }
            $jsf[0][] = $u1;
            $u2 = 0;
            if ($m24 & 1) {
                $m8 = $k2->testBit(0) + 2 * $k2->testBit(1) + 4 * $k2->testBit(2);
                $m8 += $d2;
                $m8 &= 7;
                $u2 = (($m8 == 3 || $m8 == 5) && $m14 == 2) ? -$m24 : $m24;
            }
            $jsf[1][] = $u2;
            if (2 * $d1 == $u1 + 1) {
                $d1 = 1 - $d1;
            }
            if (2 * $d2 == $u2 + 1) {
                $d2 = 1 - $d2;
            }
            $k1 = $k1->bitwise_rightShift(1);
            $k2 = $k2->bitwise_rightShift(1);
        }
        return $jsf;
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
