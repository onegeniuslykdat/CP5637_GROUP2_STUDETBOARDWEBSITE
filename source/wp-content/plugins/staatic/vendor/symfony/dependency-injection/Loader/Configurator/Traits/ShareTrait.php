<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait ShareTrait
{
    /**
     * @param bool $shared
     * @return static
     */
    final public function share($shared = \true)
    {
        $this->definition->setShared($shared);
        return $this;
    }
}
