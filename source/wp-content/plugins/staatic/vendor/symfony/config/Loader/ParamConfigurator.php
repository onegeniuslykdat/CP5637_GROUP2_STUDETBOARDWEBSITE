<?php

namespace Staatic\Vendor\Symfony\Component\Config\Loader;

class ParamConfigurator
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
        return '%' . $this->name . '%';
    }
}
