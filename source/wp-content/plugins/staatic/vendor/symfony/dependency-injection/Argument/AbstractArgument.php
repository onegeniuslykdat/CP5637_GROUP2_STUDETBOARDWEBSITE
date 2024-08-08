<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

final class AbstractArgument
{
    /**
     * @var string
     */
    private $text;
    /**
     * @var string
     */
    private $context = '';
    public function __construct(string $text = '')
    {
        $this->text = trim($text, '. ');
    }
    public function setContext(string $context): void
    {
        $this->context = $context . ' is abstract' . (('' === $this->text) ? '' : ': ');
    }
    public function getText(): string
    {
        return $this->text;
    }
    public function getTextWithContext(): string
    {
        return $this->context . $this->text . '.';
    }
}
