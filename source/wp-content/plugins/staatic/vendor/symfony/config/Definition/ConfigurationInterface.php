<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use Staatic\Vendor\Symfony\Component\Config\Definition\Builder\TreeBuilder;
interface ConfigurationInterface
{
    public function getConfigTreeBuilder();
}
