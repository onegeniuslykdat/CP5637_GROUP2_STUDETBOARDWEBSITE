<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\BoundArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\DefaultsConfigurator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\InstanceofConfigurator;
trait BindTrait
{
    /**
     * @param string $nameOrFqcn
     * @param mixed $valueOrRef
     * @return static
     */
    final public function bind($nameOrFqcn, $valueOrRef)
    {
        $valueOrRef = static::processValue($valueOrRef, \true);
        $bindings = $this->definition->getBindings();
        $type = ($this instanceof DefaultsConfigurator) ? BoundArgument::DEFAULTS_BINDING : (($this instanceof InstanceofConfigurator) ? BoundArgument::INSTANCEOF_BINDING : BoundArgument::SERVICE_BINDING);
        $bindings[$nameOrFqcn] = new BoundArgument($valueOrRef, \true, $type, $this->path ?? null);
        $this->definition->setBindings($bindings);
        return $this;
    }
}
