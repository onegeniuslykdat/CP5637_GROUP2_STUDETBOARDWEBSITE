<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
trait DeprecateTrait
{
    /**
     * @param string $package
     * @param string $version
     * @param string $message
     * @return static
     */
    final public function deprecate($package, $version, $message)
    {
        $this->definition->setDeprecated($package, $version, $message);
        return $this;
    }
}
