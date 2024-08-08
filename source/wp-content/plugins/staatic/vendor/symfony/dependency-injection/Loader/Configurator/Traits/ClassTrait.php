<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait ClassTrait
{
    /**
     * @param string|null $class
     * @return static
     */
    final public function class($class)
    {
        $this->definition->setClass($class);
        return $this;
    }
}
