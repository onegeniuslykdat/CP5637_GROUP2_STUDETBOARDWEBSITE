<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser;

class Token
{
    public const TYPE_FILE_END = 'eof';
    public const TYPE_DELIMITER = 'delimiter';
    public const TYPE_WHITESPACE = 'whitespace';
    public const TYPE_IDENTIFIER = 'identifier';
    public const TYPE_HASH = 'hash';
    public const TYPE_NUMBER = 'number';
    public const TYPE_STRING = 'string';
    /**
     * @var string|null
     */
    private $type;
    /**
     * @var string|null
     */
    private $value;
    /**
     * @var int|null
     */
    private $position;
    public function __construct(?string $type, ?string $value, ?int $position)
    {
        $this->type = $type;
        $this->value = $value;
        $this->position = $position;
    }
    public function getType(): ?int
    {
        return $this->type;
    }
    public function getValue(): ?string
    {
        return $this->value;
    }
    public function getPosition(): ?int
    {
        return $this->position;
    }
    public function isFileEnd(): bool
    {
        return self::TYPE_FILE_END === $this->type;
    }
    /**
     * @param mixed[] $values
     */
    public function isDelimiter($values = []): bool
    {
        if (self::TYPE_DELIMITER !== $this->type) {
            return \false;
        }
        if (!$values) {
            return \true;
        }
        return \in_array($this->value, $values);
    }
    public function isWhitespace(): bool
    {
        return self::TYPE_WHITESPACE === $this->type;
    }
    public function isIdentifier(): bool
    {
        return self::TYPE_IDENTIFIER === $this->type;
    }
    public function isHash(): bool
    {
        return self::TYPE_HASH === $this->type;
    }
    public function isNumber(): bool
    {
        return self::TYPE_NUMBER === $this->type;
    }
    public function isString(): bool
    {
        return self::TYPE_STRING === $this->type;
    }
    public function __toString(): string
    {
        if ($this->value) {
            return sprintf('<%s "%s" at %s>', $this->type, $this->value, $this->position);
        }
        return sprintf('<%s at %s>', $this->type, $this->position);
    }
}
