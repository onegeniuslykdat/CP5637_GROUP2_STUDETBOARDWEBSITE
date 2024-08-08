<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Node;

class Specificity
{
    public const A_FACTOR = 100;
    public const B_FACTOR = 10;
    public const C_FACTOR = 1;
    /**
     * @var int
     */
    private $a;
    /**
     * @var int
     */
    private $b;
    /**
     * @var int
     */
    private $c;
    public function __construct(int $a, int $b, int $c)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }
    /**
     * @param $this $specificity
     */
    public function plus($specificity): self
    {
        return new self($this->a + $specificity->a, $this->b + $specificity->b, $this->c + $specificity->c);
    }
    public function getValue(): int
    {
        return $this->a * self::A_FACTOR + $this->b * self::B_FACTOR + $this->c * self::C_FACTOR;
    }
    /**
     * @param $this $specificity
     */
    public function compareTo($specificity): int
    {
        if ($this->a !== $specificity->a) {
            return ($this->a > $specificity->a) ? 1 : -1;
        }
        if ($this->b !== $specificity->b) {
            return ($this->b > $specificity->b) ? 1 : -1;
        }
        if ($this->c !== $specificity->c) {
            return ($this->c > $specificity->c) ? 1 : -1;
        }
        return 0;
    }
}
