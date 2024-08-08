<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Loader;

use Closure;
use ReflectionFunction;
use ReflectionNamedType;
use InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Staatic\Vendor\Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Staatic\Vendor\Symfony\Component\Config\FileLocatorInterface;
use Staatic\Vendor\Symfony\Component\Config\Loader\FileLoader;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
class DefinitionFileLoader extends FileLoader
{
    /**
     * @var TreeBuilder
     */
    private $treeBuilder;
    /**
     * @var ContainerBuilder|null
     */
    private $container;
    public function __construct(TreeBuilder $treeBuilder, FileLocatorInterface $locator, ?ContainerBuilder $container = null)
    {
        $this->treeBuilder = $treeBuilder;
        $this->container = $container;
        parent::__construct($locator);
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     * @return mixed
     */
    public function load($resource, $type = null)
    {
        $loader = $this;
        $path = $this->locator->locate($resource);
        $this->setCurrentDir(\dirname($path));
        ($nullsafeVariable1 = $this->container) ? $nullsafeVariable1->fileExists($path) : null;
        $load = Closure::bind(static function ($file) use ($loader) {
            return include $file;
        }, null, ProtectedDefinitionFileLoader::class);
        $callback = $load($path);
        if (\is_object($callback) && \is_callable($callback)) {
            $this->executeCallback($callback, new DefinitionConfigurator($this->treeBuilder, $this, $path, $resource), $path);
        }
        return null;
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null): bool
    {
        if (!\is_string($resource)) {
            return \false;
        }
        if (null === $type && 'php' === pathinfo($resource, \PATHINFO_EXTENSION)) {
            return \true;
        }
        return 'php' === $type;
    }
    private function executeCallback(callable $callback, DefinitionConfigurator $configurator, string $path): void
    {
        $callback = Closure::fromCallable($callback);
        $arguments = [];
        $r = new ReflectionFunction($callback);
        foreach ($r->getParameters() as $parameter) {
            $reflectionType = $parameter->getType();
            if (!$reflectionType instanceof ReflectionNamedType) {
                throw new InvalidArgumentException(sprintf('Could not resolve argument "$%s" for "%s". You must typehint it (for example with "%s").', $parameter->getName(), $path, DefinitionConfigurator::class));
            }
            switch ($reflectionType->getName()) {
                case DefinitionConfigurator::class:
                    $arguments[] = $configurator;
                    break;
                case TreeBuilder::class:
                    $arguments[] = $this->treeBuilder;
                    break;
                case FileLoader::class:
                case self::class:
                    $arguments[] = $this;
                    break;
            }
        }
        $callback(...$arguments);
    }
}
final class ProtectedDefinitionFileLoader extends DefinitionFileLoader
{
}
