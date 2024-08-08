<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait AbstractTrait
{
    /**
     * @param bool $abstract
     * @return static
     */
    final public function abstract($abstract = \true)
    {
        $this->definition->setAbstract($abstract);
        return $this;
    }
}
