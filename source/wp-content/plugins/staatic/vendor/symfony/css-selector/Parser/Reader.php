<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser;

class Reader
{
    /**
     * @var string
     */
    private $source;
    /**
     * @var int
     */
    private $length;
    /**
     * @var int
     */
    private $position = 0;
    public function __construct(string $source)
    {
        $this->source = $source;
        $this->length = \strlen($source);
    }
    public function isEOF(): bool
    {
        return $this->position >= $this->length;
    }
    public function getPosition(): int
    {
        return $this->position;
    }
    public function getRemainingLength(): int
    {
        return $this->length - $this->position;
    }
    /**
     * @param int $length
     * @param int $offset
     */
    public function getSubstring($length, $offset = 0): string
    {
        return substr($this->source, $this->position + $offset, $length);
    }
    /**
     * @param string $string
     * @return bool|int
     */
    public function getOffset($string)
    {
        $position = strpos($this->source, $string, $this->position);
        return (\false === $position) ? \false : ($position - $this->position);
    }
    /**
     * @param string $pattern
     * @return mixed[]|false
     */
    public function findPattern($pattern)
    {
        $source = substr($this->source, $this->position);
        if (preg_match($pattern, $source, $matches)) {
            return $matches;
        }
        return \false;
    }
    /**
     * @param int $length
     */
    public function moveForward($length): void
    {
        $this->position += $length;
    }
    public function moveToEnd(): void
    {
        $this->position = $this->length;
    }
}
