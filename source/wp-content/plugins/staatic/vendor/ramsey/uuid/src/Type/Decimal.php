<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Type;

use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use ValueError;
use function is_numeric;
use function sprintf;
use function str_starts_with;
final class Decimal implements NumberInterface
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
        $value = (string) $value;
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Value must be a signed decimal or a string containing only ' . 'digits 0-9 and, optionally, a decimal point or sign (+ or -)');
        }
        if (strncmp($value, '+', strlen('+')) === 0) {
            $value = substr($value, 1);
        }
        if (abs((float) $value) === 0.0) {
            $value = '0';
        }
        if (strncmp($value, '-', strlen('-')) === 0) {
            $this->isNegative = \true;
        }
        $this->value = $value;
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
}
