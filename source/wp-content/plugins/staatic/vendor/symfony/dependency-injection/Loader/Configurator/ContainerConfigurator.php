<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use Staatic\Vendor\Symfony\Component\Config\Loader\ParamConfigurator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\Expression;
class ContainerConfigurator extends AbstractConfigurator
{
    public const FACTORY = 'container';
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
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $file;
    /**
     * @var int
     */
    private $anonymousCount = 0;
    /**
     * @var string|null
     */
    private $env;
    public function __construct(ContainerBuilder $container, PhpFileLoader $loader, array &$instanceof, string $path, string $file, string $env = null)
    {
        $this->container = $container;
        $this->loader = $loader;
        $this->instanceof =& $instanceof;
        $this->path = $path;
        $this->file = $file;
        $this->env = $env;
    }
    /**
     * @param string $namespace
     * @param mixed[] $config
     */
    final public function extension($namespace, $config)
    {
        if (!$this->container->hasExtension($namespace)) {
            $extensions = array_filter(array_map(function (ExtensionInterface $ext) {
                return $ext->getAlias();
            }, $this->container->getExtensions()));
            throw new InvalidArgumentException(sprintf('There is no extension able to load the configuration for "%s" (in "%s"). Looked for namespace "%s", found "%s".', $namespace, $this->file, $namespace, $extensions ? implode('", "', $extensions) : 'none'));
        }
        $this->container->loadFromExtension($namespace, static::processValue($config));
    }
    /**
     * @param string $resource
     * @param string|null $type
     * @param bool|string $ignoreErrors
     */
    final public function import($resource, $type = null, $ignoreErrors = \false)
    {
        $this->loader->setCurrentDir(\dirname($this->path));
        $this->loader->import($resource, $type, $ignoreErrors, $this->file);
    }
    final public function parameters(): ParametersConfigurator
    {
        return new ParametersConfigurator($this->container);
    }
    final public function services(): ServicesConfigurator
    {
        return new ServicesConfigurator($this->container, $this->loader, $this->instanceof, $this->path, $this->anonymousCount);
    }
    final public function env(): ?string
    {
        return $this->env;
    }
    /**
     * @param string $path
     * @return static
     */
    final public function withPath($path)
    {
        $clone = clone $this;
        $clone->path = $clone->file = $path;
        $clone->loader->setCurrentDir(\dirname($path));
        return $clone;
    }
}
function param(string $name): ParamConfigurator
{
    return new ParamConfigurator($name);
}
function service(string $serviceId): ReferenceConfigurator
{
    return new ReferenceConfigurator($serviceId);
}
function inline_service(string $class = null): InlineServiceConfigurator
{
    return new InlineServiceConfigurator(new Definition($class));
}
function service_locator(array $values): ServiceLocatorArgument
{
    return new ServiceLocatorArgument(AbstractConfigurator::processValue($values, \true));
}
function iterator(array $values): IteratorArgument
{
    return new IteratorArgument(AbstractConfigurator::processValue($values, \true));
}
/**
 * @param string|mixed[] $exclude
 */
function tagged_iterator(string $tag, string $indexAttribute = null, string $defaultIndexMethod = null, string $defaultPriorityMethod = null, $exclude = []): TaggedIteratorArgument
{
    return new TaggedIteratorArgument($tag, $indexAttribute, $defaultIndexMethod, \false, $defaultPriorityMethod, (array) $exclude);
}
/**
 * @param string|mixed[] $exclude
 */
function tagged_locator(string $tag, string $indexAttribute = null, string $defaultIndexMethod = null, string $defaultPriorityMethod = null, $exclude = []): ServiceLocatorArgument
{
    return new ServiceLocatorArgument(new TaggedIteratorArgument($tag, $indexAttribute, $defaultIndexMethod, \true, $defaultPriorityMethod, (array) $exclude));
}
function expr(string $expression): Expression
{
    return new Expression($expression);
}
function abstract_arg(string $description): AbstractArgument
{
    return new AbstractArgument($description);
}
function env(string $name): EnvConfigurator
{
    return new EnvConfigurator($name);
}
function service_closure(string $serviceId): ClosureReferenceConfigurator
{
    return new ClosureReferenceConfigurator($serviceId);
}
/**
 * @param string|mixed[]|ReferenceConfigurator|Expression $callable
 */
function closure($callable): InlineServiceConfigurator
{
    return (new InlineServiceConfigurator(new Definition('Closure')))->factory(['Closure', 'fromCallable'])->args([$callable]);
}
