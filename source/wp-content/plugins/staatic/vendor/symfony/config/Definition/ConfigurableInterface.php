<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use Staatic\Vendor\Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
interface ConfigurableInterface
{
    /**
     * @param DefinitionConfigurator $definition
     */
    public function configure($definition): void;
}
