<?php

declare (strict_types=1);
namespace Staatic\Vendor\voku\helper;

use InvalidArgumentException;
use ArrayIterator;
class SimpleHtmlAttributes implements SimpleHtmlAttributesInterface
{
    private $attributeName;
    private $element;
    private $tokens = [];
    private $previousValue;
    public function __construct($element, string $attributeName)
    {
        $this->element = $element;
        $this->attributeName = $attributeName;
        $this->tokenize();
    }
    public function __get(string $name)
    {
        if ($name === 'length') {
            $this->tokenize();
            return \count($this->tokens);
        }
        if ($name === 'value') {
            return (string) $this;
        }
        throw new InvalidArgumentException('Undefined property: $' . $name);
    }
    public function __toString(): string
    {
        $this->tokenize();
        return \implode(' ', $this->tokens);
    }
    /**
     * @param string ...$tokens
     */
    public function add(...$tokens)
    {
        if (\count($tokens) === 0) {
            return null;
        }
        foreach ($tokens as $t) {
            if (\in_array($t, $this->tokens, \true)) {
                continue;
            }
            $this->tokens[] = $t;
        }
        return $this->setAttributeValue();
    }
    /**
     * @param string $token
     */
    public function contains($token): bool
    {
        $this->tokenize();
        return \in_array($token, $this->tokens, \true);
    }
    public function entries(): ArrayIterator
    {
        $this->tokenize();
        return new ArrayIterator($this->tokens);
    }
    /**
     * @param int $index
     */
    public function item($index)
    {
        $this->tokenize();
        if ($index >= \count($this->tokens)) {
            return null;
        }
        return $this->tokens[$index];
    }
    /**
     * @param string ...$tokens
     */
    public function remove(...$tokens)
    {
        if (\count($tokens) === 0) {
            return null;
        }
        if (\count($this->tokens) === 0) {
            return null;
        }
        foreach ($tokens as $t) {
            $i = \array_search($t, $this->tokens, \true);
            if ($i === \false) {
                continue;
            }
            \array_splice($this->tokens, $i, 1);
        }
        return $this->setAttributeValue();
    }
    /**
     * @param string $old
     * @param string $new
     */
    public function replace($old, $new)
    {
        if ($old === $new) {
            return null;
        }
        $this->tokenize();
        $i = \array_search($old, $this->tokens, \true);
        if ($i !== \false) {
            $j = \array_search($new, $this->tokens, \true);
            if ($j === \false) {
                $this->tokens[$i] = $new;
            } else {
                \array_splice($this->tokens, $i, 1);
            }
            return $this->setAttributeValue();
        }
        return null;
    }
    /**
     * @param string $token
     * @param bool|null $force
     */
    public function toggle($token, $force = null): bool
    {
        $this->tokenize();
        $isThereAfter = \false;
        $i = \array_search($token, $this->tokens, \true);
        if ($force === null) {
            if ($i === \false) {
                $this->tokens[] = $token;
                $isThereAfter = \true;
            } else {
                \array_splice($this->tokens, $i, 1);
            }
        } elseif ($force) {
            if ($i === \false) {
                $this->tokens[] = $token;
            }
            $isThereAfter = \true;
        } else if ($i !== \false) {
            \array_splice($this->tokens, $i, 1);
        }
        $this->setAttributeValue();
        return $isThereAfter;
    }
    private function setAttributeValue()
    {
        if ($this->element === null) {
            return \false;
        }
        $value = \implode(' ', $this->tokens);
        if ($this->previousValue === $value) {
            return null;
        }
        $this->previousValue = $value;
        return $this->element->setAttribute($this->attributeName, $value);
    }
    private function tokenize()
    {
        if ($this->element === null) {
            return;
        }
        $current = $this->element->getAttribute($this->attributeName);
        if ($this->previousValue === $current) {
            return;
        }
        $this->previousValue = $current;
        $tokens = \explode(' ', $current);
        $finals = [];
        foreach ($tokens as $token) {
            if ($token === '') {
                continue;
            }
            if (\in_array($token, $finals, \true)) {
                continue;
            }
            $finals[] = $token;
        }
        $this->tokens = $finals;
    }
}
