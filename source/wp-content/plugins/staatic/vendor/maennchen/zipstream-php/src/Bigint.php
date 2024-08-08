<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream;

use OverflowException;
class Bigint
{
    private $bytes = [0, 0, 0, 0, 0, 0, 0, 0];
    public function __construct(int $value = 0)
    {
        $this->fillBytes($value, 0, 8);
    }
    /**
     * @param int $value
     */
    public static function init($value = 0): self
    {
        return new self($value);
    }
    /**
     * @param int $low
     * @param int $high
     */
    public static function fromLowHigh($low, $high): self
    {
        $bigint = new self();
        $bigint->fillBytes($low, 0, 4);
        $bigint->fillBytes($high, 4, 4);
        return $bigint;
    }
    public function getHigh32(): int
    {
        return $this->getValue(4, 4);
    }
    /**
     * @param int $end
     * @param int $length
     */
    public function getValue($end = 0, $length = 8): int
    {
        $result = 0;
        for ($i = $end + $length - 1; $i >= $end; $i--) {
            $result <<= 8;
            $result |= $this->bytes[$i];
        }
        return $result;
    }
    /**
     * @param bool $force
     */
    public function getLowFF($force = \false): float
    {
        if ($force || $this->isOver32()) {
            return (float) 0xffffffff;
        }
        return (float) $this->getLow32();
    }
    /**
     * @param bool $force
     */
    public function isOver32($force = \false): bool
    {
        return $force || max(array_slice($this->bytes, 4, 4)) > 0 || min(array_slice($this->bytes, 0, 4)) === 0xff;
    }
    public function getLow32(): int
    {
        return $this->getValue(0, 4);
    }
    public function getHex64(): string
    {
        $result = '0x';
        for ($i = 7; $i >= 0; $i--) {
            $result .= sprintf('%02X', $this->bytes[$i]);
        }
        return $result;
    }
    /**
     * @param $this $other
     */
    public function add($other): self
    {
        $result = clone $this;
        $overflow = \false;
        for ($i = 0; $i < 8; $i++) {
            $result->bytes[$i] += $other->bytes[$i];
            if ($overflow) {
                $result->bytes[$i]++;
                $overflow = \false;
            }
            if ($result->bytes[$i] & 0x100) {
                $overflow = \true;
                $result->bytes[$i] &= 0xff;
            }
        }
        if ($overflow) {
            throw new OverflowException();
        }
        return $result;
    }
    /**
     * @param int $value
     * @param int $start
     * @param int $count
     */
    protected function fillBytes($value, $start, $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->bytes[$start + $i] = ($i >= \PHP_INT_SIZE) ? 0 : ($value & 0xff);
            $value >>= 8;
        }
    }
}
