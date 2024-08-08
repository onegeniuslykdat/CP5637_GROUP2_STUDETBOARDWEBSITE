<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait AutowireTrait
{
    /**
     * @param bool $autowired
     * @return static
     */
    final public function autowire($autowired = \true)
    {
        $this->definition->setAutowired($autowired);
        return $this;
    }
}
