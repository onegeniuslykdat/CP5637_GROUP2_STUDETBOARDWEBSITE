<?php

namespace Staatic\Vendor\Symfony\Component\Config\Builder;

class Method
{
    /**
     * @var string
     */
    private $content;
    public function __construct(string $content)
    {
        $this->content = $content;
    }
    public function getContent(): string
    {
        return $this->content;
    }
}
