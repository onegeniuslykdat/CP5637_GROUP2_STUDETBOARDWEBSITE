<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Dumper;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
abstract class Dumper implements DumperInterface
{
    protected $container;
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }
}
