<?php

declare (strict_types=1);
namespace Staatic\Vendor\Brick\Math;

use InvalidArgumentException;
use Closure;
use LogicException;
use Staatic\Vendor\Brick\Math\Exception\DivisionByZeroException;
use Staatic\Vendor\Brick\Math\Exception\IntegerOverflowException;
use Staatic\Vendor\Brick\Math\Exception\MathException;
use Staatic\Vendor\Brick\Math\Exception\NegativeNumberException;
use Staatic\Vendor\Brick\Math\Exception\NumberFormatException;
use Staatic\Vendor\Brick\Math\Internal\Calculator;
final class BigInteger extends BigNumber
{
    /**
     * @readonly
     * @var string
     */
    private $value;
    protected function __construct(string $value)
    {
        $this->value = $value;
    }
    /**
     * @param BigNumber $number
     * @return static
     */
    protected static function from($number)
    {
        return $number->toBigInteger();
    }
    /**
     * @param string $number
     * @param int $base
     */
    public static function fromBase($number, $base): BigInteger
    {
        if ($number === '') {
            throw new NumberFormatException('The number cannot be empty.');
        }
        if ($base < 2 || $base > 36) {
            throw new InvalidArgumentException(\sprintf('Base %d is not in range 2 to 36.', $base));
        }
        if ($number[0] === '-') {
            $sign = '-';
            $number = \substr($number, 1);
        } elseif ($number[0] === '+') {
            $sign = '';
            $number = \substr($number, 1);
        } else {
            $sign = '';
        }
        if ($number === '') {
            throw new NumberFormatException('The number cannot be empty.');
        }
        $number = \ltrim($number, '0');
        if ($number === '') {
            return BigInteger::zero();
        }
        if ($number === '1') {
            return new BigInteger($sign . '1');
        }
        $pattern = '/[^' . \substr(Calculator::ALPHABET, 0, $base) . ']/';
        if (\preg_match($pattern, \strtolower($number), $matches) === 1) {
            throw new NumberFormatException(\sprintf('"%s" is not a valid character in base %d.', $matches[0], $base));
        }
        if ($base === 10) {
            return new BigInteger($sign . $number);
        }
        $result = Calculator::get()->fromBase($number, $base);
        return new BigInteger($sign . $result);
    }
    /**
     * @param string $number
     * @param string $alphabet
     */
    public static function fromArbitraryBase($number, $alphabet): BigInteger
    {
        if ($number === '') {
            throw new NumberFormatException('The number cannot be empty.');
        }
        $base = \strlen($alphabet);
        if ($base < 2) {
            throw new InvalidArgumentException('The alphabet must contain at least 2 chars.');
        }
        $pattern = '/[^' . \preg_quote($alphabet, '/') . ']/';
        if (\preg_match($pattern, $number, $matches) === 1) {
            throw NumberFormatException::charNotInAlphabet($matches[0]);
        }
        $number = Calculator::get()->fromArbitraryBase($number, $alphabet, $base);
        return new BigInteger($number);
    }
    /**
     * @param string $value
     * @param bool $signed
     */
    public static function fromBytes($value, $signed = \true): BigInteger
    {
        if ($value === '') {
            throw new NumberFormatException('The byte string must not be empty.');
        }
        $twosComplement = \false;
        if ($signed) {
            $x = \ord($value[0]);
            if ($twosComplement = $x >= 0x80) {
                $value = ~$value;
            }
        }
        $number = self::fromBase(\bin2hex($value), 16);
        if ($twosComplement) {
            return $number->plus(1)->negated();
        }
        return $number;
    }
    /**
     * @param int $numBits
     * @param callable|null $randomBytesGenerator
     */
    public static function randomBits($numBits, $randomBytesGenerator = null): BigInteger
    {
        if ($numBits < 0) {
            throw new InvalidArgumentException('The number of bits cannot be negative.');
        }
        if ($numBits === 0) {
            return BigInteger::zero();
        }
        if ($randomBytesGenerator === null) {
            $randomBytesGenerator = Closure::fromCallable('random_bytes');
        }
        $byteLength = \intdiv($numBits - 1, 8) + 1;
        $extraBits = $byteLength * 8 - $numBits;
        $bitmask = \chr(0xff >> $extraBits);
        $randomBytes = $randomBytesGenerator($byteLength);
        $randomBytes[0] = $randomBytes[0] & $bitmask;
        return self::fromBytes($randomBytes, \false);
    }
    /**
     * @param BigNumber|int|float|string $min
     * @param BigNumber|int|float|string $max
     * @param callable|null $randomBytesGenerator
     */
    public static function randomRange($min, $max, $randomBytesGenerator = null): BigInteger
    {
        $min = BigInteger::of($min);
        $max = BigInteger::of($max);
        if ($min->isGreaterThan($max)) {
            throw new MathException('$min cannot be greater than $max.');
        }
        if ($min->isEqualTo($max)) {
            return $min;
        }
        $diff = $max->minus($min);
        $bitLength = $diff->getBitLength();
        do {
            $randomNumber = self::randomBits($bitLength, $randomBytesGenerator);
        } while ($randomNumber->isGreaterThan($diff));
        return $randomNumber->plus($min);
    }
    public static function zero(): BigInteger
    {
        static $zero;
        if ($zero === null) {
            $zero = new BigInteger('0');
        }
        return $zero;
    }
    public static function one(): BigInteger
    {
        static $one;
        if ($one === null) {
            $one = new BigInteger('1');
        }
        return $one;
    }
    public static function ten(): BigInteger
    {
        static $ten;
        if ($ten === null) {
            $ten = new BigInteger('10');
        }
        return $ten;
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigInteger $a
     * @param \Staatic\Vendor\Brick\Math\BigInteger ...$n
     */
    public static function gcdMultiple($a, ...$n): BigInteger
    {
        $result = $a;
        foreach ($n as $next) {
            $result = $result->gcd($next);
            if ($result->isEqualTo(1)) {
                return $result;
            }
        }
        return $result;
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function plus($that): BigInteger
    {
        $that = BigInteger::of($that);
        if ($that->value === '0') {
            return $this;
        }
        if ($this->value === '0') {
            return $that;
        }
        $value = Calculator::get()->add($this->value, $that->value);
        return new BigInteger($value);
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function minus($that): BigInteger
    {
        $that = BigInteger::of($that);
        if ($that->value === '0') {
            return $this;
        }
        $value = Calculator::get()->sub($this->value, $that->value);
        return new BigInteger($value);
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function multipliedBy($that): BigInteger
    {
        $that = BigInteger::of($that);
        if ($that->value === '1') {
            return $this;
        }
        if ($this->value === '1') {
            return $that;
        }
        $value = Calculator::get()->mul($this->value, $that->value);
        return new BigInteger($value);
    }
    /**
     * @param BigNumber|int|float|string $that
     * @param RoundingMode $roundingMode
     */
    public function dividedBy($that, $roundingMode = RoundingMode::UNNECESSARY): BigInteger
    {
        $that = BigInteger::of($that);
        if ($that->value === '1') {
            return $this;
        }
        if ($that->value === '0') {
            throw DivisionByZeroException::divisionByZero();
        }
        $result = Calculator::get()->divRound($this->value, $that->value, $roundingMode);
        return new BigInteger($result);
    }
    /**
     * @param int $exponent
     */
    public function power($exponent): BigInteger
    {
        if ($exponent === 0) {
            return BigInteger::one();
        }
        if ($exponent === 1) {
            return $this;
        }
        if ($exponent < 0 || $exponent > Calculator::MAX_POWER) {
            throw new InvalidArgumentException(\sprintf('The exponent %d is not in the range 0 to %d.', $exponent, Calculator::MAX_POWER));
        }
        return new BigInteger(Calculator::get()->pow($this->value, $exponent));
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function quotient($that): BigInteger
    {
        $that = BigInteger::of($that);
        if ($that->value === '1') {
            return $this;
        }
        if ($that->value === '0') {
            throw DivisionByZeroException::divisionByZero();
        }
        $quotient = Calculator::get()->divQ($this->value, $that->value);
        return new BigInteger($quotient);
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function remainder($that): BigInteger
    {
        $that = BigInteger::of($that);
        if ($that->value === '1') {
            return BigInteger::zero();
        }
        if ($that->value === '0') {
            throw DivisionByZeroException::divisionByZero();
        }
        $remainder = Calculator::get()->divR($this->value, $that->value);
        return new BigInteger($remainder);
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function quotientAndRemainder($that): array
    {
        $that = BigInteger::of($that);
        if ($that->value === '0') {
            throw DivisionByZeroException::divisionByZero();
        }
        [$quotient, $remainder] = Calculator::get()->divQR($this->value, $that->value);
        return [new BigInteger($quotient), new BigInteger($remainder)];
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function mod($that): BigInteger
    {
        $that = BigInteger::of($that);
        if ($that->value === '0') {
            throw DivisionByZeroException::modulusMustNotBeZero();
        }
        $value = Calculator::get()->mod($this->value, $that->value);
        return new BigInteger($value);
    }
    /**
     * @param \Staatic\Vendor\Brick\Math\BigInteger $m
     */
    public function modInverse($m): BigInteger
    {
        if ($m->value === '0') {
            throw DivisionByZeroException::modulusMustNotBeZero();
        }
        if ($m->isNegative()) {
            throw new NegativeNumberException('Modulus must not be negative.');
        }
        if ($m->value === '1') {
            return BigInteger::zero();
        }
        $value = Calculator::get()->modInverse($this->value, $m->value);
        if ($value === null) {
            throw new MathException('Unable to compute the modInverse for the given modulus.');
        }
        return new BigInteger($value);
    }
    /**
     * @param BigNumber|int|float|string $exp
     * @param BigNumber|int|float|string $mod
     */
    public function modPow($exp, $mod): BigInteger
    {
        $exp = BigInteger::of($exp);
        $mod = BigInteger::of($mod);
        if ($this->isNegative() || $exp->isNegative() || $mod->isNegative()) {
            throw new NegativeNumberException('The operands cannot be negative.');
        }
        if ($mod->isZero()) {
            throw DivisionByZeroException::modulusMustNotBeZero();
        }
        $result = Calculator::get()->modPow($this->value, $exp->value, $mod->value);
        return new BigInteger($result);
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function gcd($that): BigInteger
    {
        $that = BigInteger::of($that);
        if ($that->value === '0' && $this->value[0] !== '-') {
            return $this;
        }
        if ($this->value === '0' && $that->value[0] !== '-') {
            return $that;
        }
        $value = Calculator::get()->gcd($this->value, $that->value);
        return new BigInteger($value);
    }
    public function sqrt(): BigInteger
    {
        if ($this->value[0] === '-') {
            throw new NegativeNumberException('Cannot calculate the square root of a negative number.');
        }
        $value = Calculator::get()->sqrt($this->value);
        return new BigInteger($value);
    }
    public function abs(): BigInteger
    {
        return $this->isNegative() ? $this->negated() : $this;
    }
    public function negated(): BigInteger
    {
        return new BigInteger(Calculator::get()->neg($this->value));
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function and($that): BigInteger
    {
        $that = BigInteger::of($that);
        return new BigInteger(Calculator::get()->and($this->value, $that->value));
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function or($that): BigInteger
    {
        $that = BigInteger::of($that);
        return new BigInteger(Calculator::get()->or($this->value, $that->value));
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function xor($that): BigInteger
    {
        $that = BigInteger::of($that);
        return new BigInteger(Calculator::get()->xor($this->value, $that->value));
    }
    public function not(): BigInteger
    {
        return $this->negated()->minus(1);
    }
    /**
     * @param int $distance
     */
    public function shiftedLeft($distance): BigInteger
    {
        if ($distance === 0) {
            return $this;
        }
        if ($distance < 0) {
            return $this->shiftedRight(-$distance);
        }
        return $this->multipliedBy(BigInteger::of(2)->power($distance));
    }
    /**
     * @param int $distance
     */
    public function shiftedRight($distance): BigInteger
    {
        if ($distance === 0) {
            return $this;
        }
        if ($distance < 0) {
            return $this->shiftedLeft(-$distance);
        }
        $operand = BigInteger::of(2)->power($distance);
        if ($this->isPositiveOrZero()) {
            return $this->quotient($operand);
        }
        return $this->dividedBy($operand, RoundingMode::UP);
    }
    public function getBitLength(): int
    {
        if ($this->value === '0') {
            return 0;
        }
        if ($this->isNegative()) {
            return $this->abs()->minus(1)->getBitLength();
        }
        return \strlen($this->toBase(2));
    }
    public function getLowestSetBit(): int
    {
        $n = $this;
        $bitLength = $this->getBitLength();
        for ($i = 0; $i <= $bitLength; $i++) {
            if ($n->isOdd()) {
                return $i;
            }
            $n = $n->shiftedRight(1);
        }
        return -1;
    }
    public function isEven(): bool
    {
        return \in_array($this->value[-1], ['0', '2', '4', '6', '8'], \true);
    }
    public function isOdd(): bool
    {
        return \in_array($this->value[-1], ['1', '3', '5', '7', '9'], \true);
    }
    /**
     * @param int $n
     */
    public function testBit($n): bool
    {
        if ($n < 0) {
            throw new InvalidArgumentException('The bit to test cannot be negative.');
        }
        return $this->shiftedRight($n)->isOdd();
    }
    /**
     * @param BigNumber|int|float|string $that
     */
    public function compareTo($that): int
    {
        $that = BigNumber::of($that);
        if ($that instanceof BigInteger) {
            return Calculator::get()->cmp($this->value, $that->value);
        }
        return -$that->compareTo($this);
    }
    public function getSign(): int
    {
        return ($this->value === '0') ? 0 : (($this->value[0] === '-') ? -1 : 1);
    }
    public function toBigInteger(): BigInteger
    {
        return $this;
    }
    public function toBigDecimal(): BigDecimal
    {
        return self::newBigDecimal($this->value);
    }
    public function toBigRational(): BigRational
    {
        return self::newBigRational($this, BigInteger::one(), \false);
    }
    /**
     * @param int $scale
     * @param RoundingMode $roundingMode
     */
    public function toScale($scale, $roundingMode = RoundingMode::UNNECESSARY): BigDecimal
    {
        return $this->toBigDecimal()->toScale($scale, $roundingMode);
    }
    public function toInt(): int
    {
        $intValue = (int) $this->value;
        if ($this->value !== (string) $intValue) {
            throw IntegerOverflowException::toIntOverflow($this);
        }
        return $intValue;
    }
    public function toFloat(): float
    {
        return (float) $this->value;
    }
    /**
     * @param int $base
     */
    public function toBase($base): string
    {
        if ($base === 10) {
            return $this->value;
        }
        if ($base < 2 || $base > 36) {
            throw new InvalidArgumentException(\sprintf('Base %d is out of range [2, 36]', $base));
        }
        return Calculator::get()->toBase($this->value, $base);
    }
    /**
     * @param string $alphabet
     */
    public function toArbitraryBase($alphabet): string
    {
        $base = \strlen($alphabet);
        if ($base < 2) {
            throw new InvalidArgumentException('The alphabet must contain at least 2 chars.');
        }
        if ($this->value[0] === '-') {
            throw new NegativeNumberException(__FUNCTION__ . '() does not support negative numbers.');
        }
        return Calculator::get()->toArbitraryBase($this->value, $alphabet, $base);
    }
    /**
     * @param bool $signed
     */
    public function toBytes($signed = \true): string
    {
        if (!$signed && $this->isNegative()) {
            throw new NegativeNumberException('Cannot convert a negative number to a byte string when $signed is false.');
        }
        $hex = $this->abs()->toBase(16);
        if (\strlen($hex) % 2 !== 0) {
            $hex = '0' . $hex;
        }
        $baseHexLength = \strlen($hex);
        if ($signed) {
            if ($this->isNegative()) {
                $bin = \hex2bin($hex);
                assert($bin !== \false);
                $hex = \bin2hex(~$bin);
                $hex = self::fromBase($hex, 16)->plus(1)->toBase(16);
                $hexLength = \strlen($hex);
                if ($hexLength < $baseHexLength) {
                    $hex = \str_repeat('0', $baseHexLength - $hexLength) . $hex;
                }
                if ($hex[0] < '8') {
                    $hex = 'FF' . $hex;
                }
            } else if ($hex[0] >= '8') {
                $hex = '00' . $hex;
            }
        }
        return \hex2bin($hex);
    }
    public function __toString(): string
    {
        return $this->value;
    }
    public function __serialize(): array
    {
        return ['value' => $this->value];
    }
    public function __unserialize(array $data): void
    {
        if (isset($this->value)) {
            throw new LogicException('__unserialize() is an internal function, it must not be called directly.');
        }
        $this->value = $data['value'];
    }
}
