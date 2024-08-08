<?php

namespace Staatic\Vendor\Symfony\Component\Config\Builder;

use Closure;
use Staatic\Vendor\Symfony\Component\Config\Definition\ConfigurationInterface;
interface ConfigBuilderGeneratorInterface
{
    /**
     * @param ConfigurationInterface $configuration
     */
    public function build($configuration): Closure;
}
