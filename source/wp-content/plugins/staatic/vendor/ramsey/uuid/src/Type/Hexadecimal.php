<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Type;

use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use ValueError;
use function preg_match;
use function sprintf;
use function substr;
final class Hexadecimal implements TypeInterface
{
    /**
     * @var string
     */
    private $value;
    /**
     * @param $this|string $value
     */
    public function __construct($value)
    {
        $this->value = ($value instanceof self) ? (string) $value : $this->prepareValue($value);
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
    private function prepareValue(string $value): string
    {
        $value = strtolower($value);
        if (strncmp($value, '0x', strlen('0x')) === 0) {
            $value = substr($value, 2);
        }
        if (!preg_match('/^[A-Fa-f0-9]+$/', $value)) {
            throw new InvalidArgumentException('Value must be a hexadecimal number');
        }
        return $value;
    }
}
