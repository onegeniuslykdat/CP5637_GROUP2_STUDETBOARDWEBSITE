<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait SyntheticTrait
{
    /**
     * @param bool $synthetic
     * @return static
     */
    final public function synthetic($synthetic = \true)
    {
        $this->definition->setSynthetic($synthetic);
        return $this;
    }
}
