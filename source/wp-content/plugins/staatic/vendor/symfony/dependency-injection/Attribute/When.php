<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute;

use Attribute;
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION | Attribute::IS_REPEATABLE)]
class When
{
    /**
     * @var string
     */
    public $env;
    public function __construct(string $env)
    {
        $this->env = $env;
    }
}
