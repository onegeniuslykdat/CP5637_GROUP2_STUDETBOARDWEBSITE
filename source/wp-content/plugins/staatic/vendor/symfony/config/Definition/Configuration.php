<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use ReflectionObject;
use Staatic\Vendor\Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Staatic\Vendor\Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Staatic\Vendor\Symfony\Component\Config\Definition\Loader\DefinitionFileLoader;
use Staatic\Vendor\Symfony\Component\Config\FileLocator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
class Configuration implements ConfigurationInterface
{
    /**
     * @var ConfigurableInterface
     */
    private $subject;
    /**
     * @var ContainerBuilder|null
     */
    private $container;
    /**
     * @var string
     */
    private $alias;
    public function __construct(ConfigurableInterface $subject, ?ContainerBuilder $container, string $alias)
    {
        $this->subject = $subject;
        $this->container = $container;
        $this->alias = $alias;
    }
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->alias);
        $file = (new ReflectionObject($this->subject))->getFileName();
        $loader = new DefinitionFileLoader($treeBuilder, new FileLocator(\dirname($file)), $this->container);
        $configurator = new DefinitionConfigurator($treeBuilder, $loader, $file, $file);
        $this->subject->configure($configurator);
        return $treeBuilder;
    }
}
