<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use LogicException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Alias;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ChildDefinition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
class ServicesConfigurator extends AbstractConfigurator
{
    public const FACTORY = 'services';
    /**
     * @var Definition
     */
    private $defaults;
    /**
     * @var ContainerBuilder
     */
    private $container;
    /**
     * @var PhpFileLoader
     */
    private $loader;
    /**
     * @var mixed[]
     */
    private $instanceof;
    /**
     * @var string|null
     */
    private $path;
    /**
     * @var string
     */
    private $anonymousHash;
    /**
     * @var int
     */
    private $anonymousCount;
    public function __construct(ContainerBuilder $container, PhpFileLoader $loader, array &$instanceof, string $path = null, int &$anonymousCount = 0)
    {
        $this->defaults = new Definition();
        $this->container = $container;
        $this->loader = $loader;
        $this->instanceof =& $instanceof;
        $this->path = $path;
        $this->anonymousHash = ContainerBuilder::hash($path ?: mt_rand());
        $this->anonymousCount =& $anonymousCount;
        $instanceof = [];
    }
    final public function defaults(): DefaultsConfigurator
    {
        return new DefaultsConfigurator($this, $this->defaults = new Definition(), $this->path);
    }
    /**
     * @param string $fqcn
     */
    final public function instanceof($fqcn): InstanceofConfigurator
    {
        $this->instanceof[$fqcn] = $definition = new ChildDefinition('');
        return new InstanceofConfigurator($this, $definition, $fqcn, $this->path);
    }
    /**
     * @param string|null $id
     * @param string|null $class
     */
    final public function set($id, $class = null): ServiceConfigurator
    {
        $defaults = $this->defaults;
        $definition = new Definition();
        if (null === $id) {
            if (!$class) {
                throw new LogicException('Anonymous services must have a class name.');
            }
            $id = sprintf('.%d_%s', ++$this->anonymousCount, preg_replace('/^.*\\\\/', '', $class) . '~' . $this->anonymousHash);
        } elseif (!$defaults->isPublic() || !$defaults->isPrivate()) {
            $definition->setPublic($defaults->isPublic() && !$defaults->isPrivate());
        }
        $definition->setAutowired($defaults->isAutowired());
        $definition->setAutoconfigured($defaults->isAutoconfigured());
        $definition->setBindings(unserialize(serialize($defaults->getBindings())));
        $definition->setChanges([]);
        $configurator = new ServiceConfigurator($this->container, $this->instanceof, \true, $this, $definition, $id, $defaults->getTags(), $this->path);
        return (null !== $class) ? $configurator->class($class) : $configurator;
    }
    /**
     * @param string $id
     * @return static
     */
    final public function remove($id)
    {
        $this->container->removeDefinition($id);
        $this->container->removeAlias($id);
        return $this;
    }
    /**
     * @param string $id
     * @param string $referencedId
     */
    final public function alias($id, $referencedId): AliasConfigurator
    {
        $ref = static::processValue($referencedId, \true);
        $alias = new Alias((string) $ref);
        if (!$this->defaults->isPublic() || !$this->defaults->isPrivate()) {
            $alias->setPublic($this->defaults->isPublic());
        }
        $this->container->setAlias($id, $alias);
        return new AliasConfigurator($this, $alias);
    }
    /**
     * @param string $namespace
     * @param string $resource
     */
    final public function load($namespace, $resource): PrototypeConfigurator
    {
        return new PrototypeConfigurator($this, $this->loader, $this->defaults, $namespace, $resource, \true, $this->path);
    }
    /**
     * @param string $id
     */
    final public function get($id): ServiceConfigurator
    {
        $definition = $this->container->getDefinition($id);
        return new ServiceConfigurator($this->container, $definition->getInstanceofConditionals(), \true, $this, $definition, $id, []);
    }
    /**
     * @param string $id
     * @param mixed[] $services
     */
    final public function stack($id, $services): AliasConfigurator
    {
        foreach ($services as $i => $service) {
            if ($service instanceof InlineServiceConfigurator) {
                $definition = $service->definition->setInstanceofConditionals($this->instanceof);
                $changes = $definition->getChanges();
                $definition->setAutowired((isset($changes['autowired']) ? $definition : $this->defaults)->isAutowired());
                $definition->setAutoconfigured((isset($changes['autoconfigured']) ? $definition : $this->defaults)->isAutoconfigured());
                $definition->setBindings(array_merge($this->defaults->getBindings(), $definition->getBindings()));
                $definition->setChanges($changes);
                $services[$i] = $definition;
            } elseif (!$service instanceof ReferenceConfigurator) {
                throw new InvalidArgumentException(sprintf('"%s()" expects a list of definitions as returned by "%s()" or "%s()", "%s" given at index "%s" for service "%s".', __METHOD__, InlineServiceConfigurator::FACTORY, ReferenceConfigurator::FACTORY, ($service instanceof AbstractConfigurator) ? $service::FACTORY . '()' : get_debug_type($service), $i, $id));
            }
        }
        $alias = $this->alias($id, '');
        $alias->definition = $this->set($id)->parent('')->args($services)->tag('container.stack')->definition;
        return $alias;
    }
    final public function __invoke(string $id, string $class = null): ServiceConfigurator
    {
        return $this->set($id, $class);
    }
    public function __destruct()
    {
        $this->loader->registerAliasesForSinglyImplementedInterfaces();
    }
}
