<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Configurator;

use Staatic\Vendor\Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Staatic\Vendor\Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Staatic\Vendor\Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Staatic\Vendor\Symfony\Component\Config\Definition\Loader\DefinitionFileLoader;
class DefinitionConfigurator
{
    /**
     * @var TreeBuilder
     */
    private $treeBuilder;
    /**
     * @var DefinitionFileLoader
     */
    private $loader;
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $file;
    public function __construct(TreeBuilder $treeBuilder, DefinitionFileLoader $loader, string $path, string $file)
    {
        $this->treeBuilder = $treeBuilder;
        $this->loader = $loader;
        $this->path = $path;
        $this->file = $file;
    }
    /**
     * @param string $resource
     * @param string|null $type
     * @param bool $ignoreErrors
     */
    public function import($resource, $type = null, $ignoreErrors = \false): void
    {
        $this->loader->setCurrentDir(\dirname($this->path));
        $this->loader->import($resource, $type, $ignoreErrors, $this->file);
    }
    /**
     * @return NodeDefinition|ArrayNodeDefinition
     */
    public function rootNode()
    {
        return $this->treeBuilder->getRootNode();
    }
    /**
     * @param string $separator
     */
    public function setPathSeparator($separator): void
    {
        $this->treeBuilder->setPathSeparator($separator);
    }
}
