<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait LazyTrait
{
    /**
     * @param bool|string $lazy
     * @return static
     */
    final public function lazy($lazy = \true)
    {
        $this->definition->setLazy((bool) $lazy);
        if (\is_string($lazy)) {
            $this->definition->addTag('proxy', ['interface' => $lazy]);
        }
        return $this;
    }
}
