<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves;

use RuntimeException;
use UnexpectedValueException;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
use Staatic\Vendor\phpseclib3\Math\PrimeField;
use Staatic\Vendor\phpseclib3\Math\PrimeField\Integer as PrimeInteger;
class TwistedEdwards extends Base
{
    protected $modulo;
    protected $a;
    protected $d;
    protected $p;
    protected $zero;
    protected $one;
    protected $two;
    /**
     * @param BigInteger $modulo
     */
    public function setModulo($modulo)
    {
        $this->modulo = $modulo;
        $this->factory = new PrimeField($modulo);
        $this->zero = $this->factory->newInteger(new BigInteger(0));
        $this->one = $this->factory->newInteger(new BigInteger(1));
        $this->two = $this->factory->newInteger(new BigInteger(2));
    }
    /**
     * @param BigInteger $a
     * @param BigInteger $d
     */
    public function setCoefficients($a, $d)
    {
        if (!isset($this->factory)) {
            throw new RuntimeException('setModulo needs to be called before this method');
        }
        $this->a = $this->factory->newInteger($a);
        $this->d = $this->factory->newInteger($d);
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
    public function getA()
    {
        return $this->a;
    }
    public function getD()
    {
        return $this->d;
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
     */
    public function convertToAffine($p)
    {
        if (!isset($p[2])) {
            return $p;
        }
        list($x, $y, $z) = $p;
        $z = $this->one->divide($z);
        return [$x->multiply($z), $y->multiply($z)];
    }
    public function getModulo()
    {
        return $this->modulo;
    }
    /**
     * @param mixed[] $p
     */
    public function verifyPoint($p)
    {
        list($x, $y) = $p;
        $x2 = $x->multiply($x);
        $y2 = $y->multiply($y);
        $lhs = $this->a->multiply($x2)->add($y2);
        $rhs = $this->d->multiply($x2)->multiply($y2)->add($this->one);
        return $lhs->equals($rhs);
    }
}
