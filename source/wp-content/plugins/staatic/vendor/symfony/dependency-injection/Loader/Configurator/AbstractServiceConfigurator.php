<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
abstract class AbstractServiceConfigurator extends AbstractConfigurator
{
    protected $parent;
    protected $id;
    /**
     * @var mixed[]
     */
    private $defaultTags = [];
    public function __construct(ServicesConfigurator $parent, Definition $definition, string $id = null, array $defaultTags = [])
    {
        $this->parent = $parent;
        $this->definition = $definition;
        $this->id = $id;
        $this->defaultTags = $defaultTags;
    }
    public function __destruct()
    {
        foreach ($this->defaultTags as $name => $attributes) {
            foreach ($attributes as $attribute) {
                $this->definition->addTag($name, $attribute);
            }
        }
        $this->defaultTags = [];
    }
    /**
     * @param string|null $id
     * @param string|null $class
     */
    final public function set($id, $class = null): ServiceConfigurator
    {
        $this->__destruct();
        return $this->parent->set($id, $class);
    }
    /**
     * @param string $id
     * @param string $referencedId
     */
    final public function alias($id, $referencedId): AliasConfigurator
    {
        $this->__destruct();
        return $this->parent->alias($id, $referencedId);
    }
    /**
     * @param string $namespace
     * @param string $resource
     */
    final public function load($namespace, $resource): PrototypeConfigurator
    {
        $this->__destruct();
        return $this->parent->load($namespace, $resource);
    }
    /**
     * @param string $id
     */
    final public function get($id): ServiceConfigurator
    {
        $this->__destruct();
        return $this->parent->get($id);
    }
    /**
     * @param string $id
     */
    final public function remove($id): ServicesConfigurator
    {
        $this->__destruct();
        return $this->parent->remove($id);
    }
    /**
     * @param string $id
     * @param mixed[] $services
     */
    final public function stack($id, $services): AliasConfigurator
    {
        $this->__destruct();
        return $this->parent->stack($id, $services);
    }
    final public function __invoke(string $id, string $class = null): ServiceConfigurator
    {
        $this->__destruct();
        return $this->parent->set($id, $class);
    }
}
