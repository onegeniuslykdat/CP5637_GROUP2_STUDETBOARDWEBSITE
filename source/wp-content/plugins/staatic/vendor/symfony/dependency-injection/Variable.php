<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

class Variable
{
    /**
     * @var string
     */
    private $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    public function __toString(): string
    {
        return $this->name;
    }
}
