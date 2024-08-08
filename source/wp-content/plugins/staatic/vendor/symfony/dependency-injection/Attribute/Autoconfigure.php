<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute;

use Attribute;
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Autoconfigure
{
    /**
     * @var mixed[]|null
     */
    public $tags;
    /**
     * @var mixed[]|null
     */
    public $calls;
    /**
     * @var mixed[]|null
     */
    public $bind;
    /**
     * @var bool|string|null
     */
    public $lazy = null;
    /**
     * @var bool|null
     */
    public $public;
    /**
     * @var bool|null
     */
    public $shared;
    /**
     * @var bool|null
     */
    public $autowire;
    /**
     * @var mixed[]|null
     */
    public $properties;
    /**
     * @var mixed[]|string|null
     */
    public $configurator = null;
    /**
     * @param bool|string|null $lazy
     * @param mixed[]|string|null $configurator
     */
    public function __construct(?array $tags = null, ?array $calls = null, ?array $bind = null, $lazy = null, ?bool $public = null, ?bool $shared = null, ?bool $autowire = null, ?array $properties = null, $configurator = null)
    {
        $this->tags = $tags;
        $this->calls = $calls;
        $this->bind = $bind;
        $this->lazy = $lazy;
        $this->public = $public;
        $this->shared = $shared;
        $this->autowire = $autowire;
        $this->properties = $properties;
        $this->configurator = $configurator;
    }
}
