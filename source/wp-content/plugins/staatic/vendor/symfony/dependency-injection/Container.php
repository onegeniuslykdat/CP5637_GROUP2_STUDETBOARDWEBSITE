<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use Closure;
use Exception;
use Throwable;
use UnitEnum;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocator as ArgumentServiceLocator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
class_exists(RewindableGenerator::class);
class_exists(ArgumentServiceLocator::class);
class Container implements ContainerInterface, ResetInterface
{
    protected $parameterBag;
    protected $services = [];
    protected $privates = [];
    protected $fileMap = [];
    protected $methodMap = [];
    protected $factories = [];
    protected $aliases = [];
    protected $loading = [];
    protected $resolving = [];
    protected $syntheticIds = [];
    /**
     * @var mixed[]
     */
    private $envCache = [];
    /**
     * @var bool
     */
    private $compiled = \false;
    /**
     * @var Closure
     */
    private $getEnv;
    public function __construct(ParameterBagInterface $parameterBag = null)
    {
        $this->parameterBag = $parameterBag ?? new EnvPlaceholderParameterBag();
    }
    public function compile()
    {
        $this->parameterBag->resolve();
        $this->parameterBag = new FrozenParameterBag($this->parameterBag->all());
        $this->compiled = \true;
    }
    public function isCompiled(): bool
    {
        return $this->compiled;
    }
    public function getParameterBag(): ParameterBagInterface
    {
        return $this->parameterBag;
    }
    /**
     * @param string $name
     */
    public function getParameter($name)
    {
        return $this->parameterBag->get($name);
    }
    /**
     * @param string $name
     */
    public function hasParameter($name): bool
    {
        return $this->parameterBag->has($name);
    }
    /**
     * @param string $name
     * @param mixed[]|bool|string|int|float|UnitEnum|null $value
     */
    public function setParameter($name, $value)
    {
        $this->parameterBag->set($name, $value);
    }
    /**
     * @param string $id
     * @param object|null $service
     */
    public function set($id, $service)
    {
        if (isset($this->privates['service_container']) && $this->privates['service_container'] instanceof Closure) {
            $initialize = $this->privates['service_container'];
            unset($this->privates['service_container']);
            $initialize();
        }
        if ('service_container' === $id) {
            throw new InvalidArgumentException('You cannot set service "service_container".');
        }
        if (!(isset($this->fileMap[$id]) || isset($this->methodMap[$id]))) {
            if (isset($this->syntheticIds[$id]) || !isset($this->getRemovedIds()[$id])) {
            } elseif (null === $service) {
                throw new InvalidArgumentException(sprintf('The "%s" service is private, you cannot unset it.', $id));
            } else {
                throw new InvalidArgumentException(sprintf('The "%s" service is private, you cannot replace it.', $id));
            }
        } elseif (isset($this->services[$id])) {
            throw new InvalidArgumentException(sprintf('The "%s" service is already initialized, you cannot replace it.', $id));
        }
        if (isset($this->aliases[$id])) {
            unset($this->aliases[$id]);
        }
        if (null === $service) {
            unset($this->services[$id]);
            return;
        }
        $this->services[$id] = $service;
    }
    /**
     * @param string $id
     */
    public function has($id): bool
    {
        if (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }
        if (isset($this->services[$id])) {
            return \true;
        }
        if ('service_container' === $id) {
            return \true;
        }
        return isset($this->fileMap[$id]) || isset($this->methodMap[$id]);
    }
    /**
     * @param string $id
     * @param int $invalidBehavior
     */
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->services[$id] ?? $this->services[$id = $this->aliases[$id] ?? $id] ?? (('service_container' === $id) ? $this : ($this->factories[$id] ?? Closure::fromCallable([$this, 'make']))($id, $invalidBehavior));
    }
    private function make(string $id, int $invalidBehavior)
    {
        if (isset($this->loading[$id])) {
            throw new ServiceCircularReferenceException($id, array_merge(array_keys($this->loading), [$id]));
        }
        $this->loading[$id] = \true;
        try {
            if (isset($this->fileMap[$id])) {
                return (4 === $invalidBehavior) ? null : $this->load($this->fileMap[$id]);
            } elseif (isset($this->methodMap[$id])) {
                return (4 === $invalidBehavior) ? null : $this->{$this->methodMap[$id]}();
            }
        } catch (Exception $e) {
            unset($this->services[$id]);
            throw $e;
        } finally {
            unset($this->loading[$id]);
        }
        if (self::EXCEPTION_ON_INVALID_REFERENCE === $invalidBehavior) {
            if (!$id) {
                throw new ServiceNotFoundException($id);
            }
            if (isset($this->syntheticIds[$id])) {
                throw new ServiceNotFoundException($id, null, null, [], sprintf('The "%s" service is synthetic, it needs to be set at boot time before it can be used.', $id));
            }
            if (isset($this->getRemovedIds()[$id])) {
                throw new ServiceNotFoundException($id, null, null, [], sprintf('The "%s" service or alias has been removed or inlined when the container was compiled. You should either make it public, or stop using the container directly and use dependency injection instead.', $id));
            }
            $alternatives = [];
            foreach ($this->getServiceIds() as $knownId) {
                if ('' === $knownId || '.' === $knownId[0]) {
                    continue;
                }
                $lev = levenshtein($id, $knownId);
                if ($lev <= \strlen($id) / 3 || strpos($knownId, $id) !== false) {
                    $alternatives[] = $knownId;
                }
            }
            throw new ServiceNotFoundException($id, null, null, $alternatives);
        }
        return null;
    }
    /**
     * @param string $id
     */
    public function initialized($id): bool
    {
        if (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }
        if ('service_container' === $id) {
            return \false;
        }
        return isset($this->services[$id]);
    }
    public function reset()
    {
        $services = $this->services + $this->privates;
        $this->services = $this->factories = $this->privates = [];
        foreach ($services as $service) {
            try {
                if ($service instanceof ResetInterface) {
                    $service->reset();
                }
            } catch (Throwable $exception) {
                continue;
            }
        }
    }
    public function getServiceIds(): array
    {
        return array_map('strval', array_unique(array_merge(['service_container'], array_keys($this->fileMap), array_keys($this->methodMap), array_keys($this->aliases), array_keys($this->services))));
    }
    public function getRemovedIds(): array
    {
        return [];
    }
    /**
     * @param string $id
     */
    public static function camelize($id): string
    {
        return strtr(ucwords(strtr($id, ['_' => ' ', '.' => '_ ', '\\' => '_ '])), [' ' => '']);
    }
    /**
     * @param string $id
     */
    public static function underscore($id): string
    {
        return strtolower(preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], ['\1_\2', '\1_\2'], str_replace('_', '.', $id)));
    }
    /**
     * @param string $file
     */
    protected function load($file)
    {
        return require $file;
    }
    /**
     * @param string $name
     * @return mixed
     */
    protected function getEnv($name)
    {
        if (isset($this->resolving[$envName = "env({$name})"])) {
            throw new ParameterCircularReferenceException(array_keys($this->resolving));
        }
        if (isset($this->envCache[$name]) || \array_key_exists($name, $this->envCache)) {
            return $this->envCache[$name];
        }
        if (!$this->has($id = 'container.env_var_processors_locator')) {
            $this->set($id, new ServiceLocator([]));
        }
        $this->getEnv = $this->getEnv ?? Closure::fromCallable([$this, 'getEnv']);
        $processors = $this->get($id);
        if (\false !== $i = strpos($name, ':')) {
            $prefix = substr($name, 0, $i);
            $localName = substr($name, 1 + $i);
        } else {
            $prefix = 'string';
            $localName = $name;
        }
        if ($processors->has($prefix)) {
            $processor = $processors->get($prefix);
        } else {
            $processor = new EnvVarProcessor($this);
            if (\false === $i) {
                $prefix = '';
            }
        }
        $this->resolving[$envName] = \true;
        try {
            return $this->envCache[$name] = $processor->getEnv($prefix, $localName, $this->getEnv);
        } finally {
            unset($this->resolving[$envName]);
        }
    }
    /**
     * @param string|false $registry
     * @param string $id
     * @param string|null $method
     * @param string|bool $load
     * @return mixed
     */
    final protected function getService($registry, $id, $method, $load)
    {
        if ('service_container' === $id) {
            return $this;
        }
        if (\is_string($load)) {
            throw new RuntimeException($load);
        }
        if (null === $method) {
            return (\false !== $registry) ? $this->{$registry}[$id] ?? null : null;
        }
        if (\false !== $registry) {
            return $this->{$registry}[$id] = $this->{$registry}[$id] ?? ($load ? $this->load($method) : $this->{$method}());
        }
        if (!$load) {
            return $this->{$method}();
        }
        return ($factory = $this->factories[$id] ?? $this->factories['service_container'][$id] ?? null) ? $factory() : $this->load($method);
    }
    private function __clone()
    {
    }
}
