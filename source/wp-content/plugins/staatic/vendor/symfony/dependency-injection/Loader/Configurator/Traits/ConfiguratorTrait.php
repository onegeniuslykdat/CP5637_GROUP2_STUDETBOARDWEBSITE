<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
trait ConfiguratorTrait
{
    /**
     * @param string|mixed[]|ReferenceConfigurator $configurator
     * @return static
     */
    final public function configurator($configurator)
    {
        $this->definition->setConfigurator(static::processValue($configurator, \true));
        return $this;
    }
}
