<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Type;

use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use ValueError;
use function assert;
use function is_numeric;
use function preg_match;
use function sprintf;
use function substr;
final class Integer implements NumberInterface
{
    /**
     * @var string
     */
    private $value;
    /**
     * @var bool
     */
    private $isNegative = \false;
    /**
     * @param float|int|string|$this $value
     */
    public function __construct($value)
    {
        $this->value = ($value instanceof self) ? (string) $value : $this->prepareValue($value);
    }
    public function isNegative(): bool
    {
        return $this->isNegative;
    }
    public function toString(): string
    {
        return $this->value;
    }
    public function __toString(): string
    {
        return $this->toString();
    }
    public function jsonSerialize(): string
    {
        return $this->toString();
    }
    public function serialize(): string
    {
        return $this->toString();
    }
    public function __serialize(): array
    {
        return ['string' => $this->toString()];
    }
    public function unserialize($data): void
    {
        $this->__construct($data);
    }
    public function __unserialize(array $data): void
    {
        if (!isset($data['string'])) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }
        $this->unserialize($data['string']);
    }
    /**
     * @param float|int|string $value
     */
    private function prepareValue($value): string
    {
        $value = (string) $value;
        $sign = '+';
        if (strncmp($value, '-', strlen('-')) === 0 || strncmp($value, '+', strlen('+')) === 0) {
            $sign = substr($value, 0, 1);
            $value = substr($value, 1);
        }
        if (!preg_match('/^\d+$/', $value)) {
            throw new InvalidArgumentException('Value must be a signed integer or a string containing only ' . 'digits 0-9 and, optionally, a sign (+ or -)');
        }
        $value = ltrim($value, '0');
        if ($value === '') {
            $value = '0';
        }
        if ($sign === '-' && $value !== '0') {
            $value = $sign . $value;
            $this->isNegative = \true;
        }
        assert(is_numeric($value));
        return $value;
    }
}
