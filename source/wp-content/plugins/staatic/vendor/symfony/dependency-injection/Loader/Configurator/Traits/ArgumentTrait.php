<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait ArgumentTrait
{
    /**
     * @param mixed[] $arguments
     * @return static
     */
    final public function args($arguments)
    {
        $this->definition->setArguments(static::processValue($arguments, \true));
        return $this;
    }
    /**
     * @param string|int $key
     * @param mixed $value
     * @return static
     */
    final public function arg($key, $value)
    {
        $this->definition->setArgument($key, static::processValue($value, \true));
        return $this;
    }
}
