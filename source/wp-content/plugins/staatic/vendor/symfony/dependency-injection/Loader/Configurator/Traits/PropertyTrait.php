<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait PropertyTrait
{
    /**
     * @param string $name
     * @param mixed $value
     * @return static
     */
    final public function property($name, $value)
    {
        $this->definition->setProperty($name, static::processValue($value, \true));
        return $this;
    }
}
