<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait PublicTrait
{
    /**
     * @return static
     */
    final public function public()
    {
        $this->definition->setPublic(\true);
        return $this;
    }
    /**
     * @return static
     */
    final public function private()
    {
        $this->definition->setPublic(\false);
        return $this;
    }
}
