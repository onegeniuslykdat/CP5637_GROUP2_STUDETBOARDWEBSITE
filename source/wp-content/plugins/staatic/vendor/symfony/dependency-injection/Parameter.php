<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

class Parameter
{
    /**
     * @var string
     */
    private $id;
    public function __construct(string $id)
    {
        $this->id = $id;
    }
    public function __toString(): string
    {
        return $this->id;
    }
}
