<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use Closure;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\BoundArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;
class Definition
{
    private const DEFAULT_DEPRECATION_TEMPLATE = 'The "%service_id%" service is deprecated. You should stop using it, as it will be removed in the future.';
    /**
     * @var string|null
     */
    private $class;
    /**
     * @var string|null
     */
    private $file;
    /**
     * @var mixed[]|string|null
     */
    private $factory = null;
    /**
     * @var bool
     */
    private $shared = \true;
    /**
     * @var mixed[]
     */
    private $deprecation = [];
    /**
     * @var mixed[]
     */
    private $properties = [];
    /**
     * @var mixed[]
     */
    private $calls = [];
    /**
     * @var mixed[]
     */
    private $instanceof = [];
    /**
     * @var bool
     */
    private $autoconfigured = \false;
    /**
     * @var mixed[]|string|null
     */
    private $configurator = null;
    /**
     * @var mixed[]
     */
    private $tags = [];
    /**
     * @var bool
     */
    private $public = \false;
    /**
     * @var bool
     */
    private $synthetic = \false;
    /**
     * @var bool
     */
    private $abstract = \false;
    /**
     * @var bool
     */
    private $lazy = \false;
    /**
     * @var mixed[]|null
     */
    private $decoratedService;
    /**
     * @var bool
     */
    private $autowired = \false;
    /**
     * @var mixed[]
     */
    private $changes = [];
    /**
     * @var mixed[]
     */
    private $bindings = [];
    /**
     * @var mixed[]
     */
    private $errors = [];
    protected $arguments = [];
    /**
     * @var string|null
     */
    public $innerServiceId;
    /**
     * @var int|null
     */
    public $decorationOnInvalid;
    public function __construct(string $class = null, array $arguments = [])
    {
        if (null !== $class) {
            $this->setClass($class);
        }
        $this->arguments = $arguments;
    }
    public function getChanges(): array
    {
        return $this->changes;
    }
    /**
     * @param mixed[] $changes
     * @return static
     */
    public function setChanges($changes)
    {
        $this->changes = $changes;
        return $this;
    }
    /**
     * @param string|mixed[]|Reference|null $factory
     * @return static
     */
    public function setFactory($factory)
    {
        $this->changes['factory'] = \true;
        if (\is_string($factory) && strpos($factory, '::') !== false) {
            $factory = explode('::', $factory, 2);
        } elseif ($factory instanceof Reference) {
            $factory = [$factory, '__invoke'];
        }
        $this->factory = $factory;
        return $this;
    }
    /**
     * @return mixed[]|string|null
     */
    public function getFactory()
    {
        return $this->factory;
    }
    /**
     * @param string|null $id
     * @param string|null $renamedId
     * @param int $priority
     * @param int $invalidBehavior
     * @return static
     */
    public function setDecoratedService($id, $renamedId = null, $priority = 0, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if ($renamedId && $id === $renamedId) {
            throw new InvalidArgumentException(sprintf('The decorated service inner name for "%s" must be different than the service name itself.', $id));
        }
        $this->changes['decorated_service'] = \true;
        if (null === $id) {
            $this->decoratedService = null;
        } else {
            $this->decoratedService = [$id, $renamedId, $priority];
            if (ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE !== $invalidBehavior) {
                $this->decoratedService[] = $invalidBehavior;
            }
        }
        return $this;
    }
    public function getDecoratedService(): ?array
    {
        return $this->decoratedService;
    }
    /**
     * @param string|null $class
     * @return static
     */
    public function setClass($class)
    {
        $this->changes['class'] = \true;
        $this->class = $class;
        return $this;
    }
    public function getClass(): ?string
    {
        return $this->class;
    }
    /**
     * @param mixed[] $arguments
     * @return static
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }
    /**
     * @param mixed[] $properties
     * @return static
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
        return $this;
    }
    public function getProperties(): array
    {
        return $this->properties;
    }
    /**
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }
    /**
     * @param mixed $argument
     * @return static
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
        return $this;
    }
    /**
     * @param int|string $index
     * @param mixed $argument
     * @return static
     */
    public function replaceArgument($index, $argument)
    {
        if (0 === \count($this->arguments)) {
            throw new OutOfBoundsException(sprintf('Cannot replace arguments for class "%s" if none have been configured yet.', $this->class));
        }
        if (\is_int($index) && ($index < 0 || $index > \count($this->arguments) - 1)) {
            throw new OutOfBoundsException(sprintf('The index "%d" is not in the range [0, %d] of the arguments of class "%s".', $index, \count($this->arguments) - 1, $this->class));
        }
        if (!\array_key_exists($index, $this->arguments)) {
            throw new OutOfBoundsException(sprintf('The argument "%s" doesn\'t exist in class "%s".', $index, $this->class));
        }
        $this->arguments[$index] = $argument;
        return $this;
    }
    /**
     * @param int|string $key
     * @param mixed $value
     * @return static
     */
    public function setArgument($key, $value)
    {
        $this->arguments[$key] = $value;
        return $this;
    }
    public function getArguments(): array
    {
        return $this->arguments;
    }
    /**
     * @param int|string $index
     * @return mixed
     */
    public function getArgument($index)
    {
        if (!\array_key_exists($index, $this->arguments)) {
            throw new OutOfBoundsException(sprintf('The argument "%s" doesn\'t exist in class "%s".', $index, $this->class));
        }
        return $this->arguments[$index];
    }
    /**
     * @param mixed[] $calls
     * @return static
     */
    public function setMethodCalls($calls = [])
    {
        $this->calls = [];
        foreach ($calls as $call) {
            $this->addMethodCall($call[0], $call[1], $call[2] ?? \false);
        }
        return $this;
    }
    /**
     * @param string $method
     * @param mixed[] $arguments
     * @param bool $returnsClone
     * @return static
     */
    public function addMethodCall($method, $arguments = [], $returnsClone = \false)
    {
        if (empty($method)) {
            throw new InvalidArgumentException('Method name cannot be empty.');
        }
        $this->calls[] = $returnsClone ? [$method, $arguments, \true] : [$method, $arguments];
        return $this;
    }
    /**
     * @param string $method
     * @return static
     */
    public function removeMethodCall($method)
    {
        foreach ($this->calls as $i => $call) {
            if ($call[0] === $method) {
                unset($this->calls[$i]);
            }
        }
        return $this;
    }
    /**
     * @param string $method
     */
    public function hasMethodCall($method): bool
    {
        foreach ($this->calls as $call) {
            if ($call[0] === $method) {
                return \true;
            }
        }
        return \false;
    }
    public function getMethodCalls(): array
    {
        return $this->calls;
    }
    /**
     * @param mixed[] $instanceof
     * @return static
     */
    public function setInstanceofConditionals($instanceof)
    {
        $this->instanceof = $instanceof;
        return $this;
    }
    public function getInstanceofConditionals(): array
    {
        return $this->instanceof;
    }
    /**
     * @param bool $autoconfigured
     * @return static
     */
    public function setAutoconfigured($autoconfigured)
    {
        $this->changes['autoconfigured'] = \true;
        $this->autoconfigured = $autoconfigured;
        return $this;
    }
    public function isAutoconfigured(): bool
    {
        return $this->autoconfigured;
    }
    /**
     * @param mixed[] $tags
     * @return static
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }
    public function getTags(): array
    {
        return $this->tags;
    }
    /**
     * @param string $name
     */
    public function getTag($name): array
    {
        return $this->tags[$name] ?? [];
    }
    /**
     * @param string $name
     * @param mixed[] $attributes
     * @return static
     */
    public function addTag($name, $attributes = [])
    {
        $this->tags[$name][] = $attributes;
        return $this;
    }
    /**
     * @param string $name
     */
    public function hasTag($name): bool
    {
        return isset($this->tags[$name]);
    }
    /**
     * @param string $name
     * @return static
     */
    public function clearTag($name)
    {
        unset($this->tags[$name]);
        return $this;
    }
    /**
     * @return static
     */
    public function clearTags()
    {
        $this->tags = [];
        return $this;
    }
    /**
     * @param string|null $file
     * @return static
     */
    public function setFile($file)
    {
        $this->changes['file'] = \true;
        $this->file = $file;
        return $this;
    }
    public function getFile(): ?string
    {
        return $this->file;
    }
    /**
     * @param bool $shared
     * @return static
     */
    public function setShared($shared)
    {
        $this->changes['shared'] = \true;
        $this->shared = $shared;
        return $this;
    }
    public function isShared(): bool
    {
        return $this->shared;
    }
    /**
     * @param bool $boolean
     * @return static
     */
    public function setPublic($boolean)
    {
        $this->changes['public'] = \true;
        $this->public = $boolean;
        return $this;
    }
    public function isPublic(): bool
    {
        return $this->public;
    }
    public function isPrivate(): bool
    {
        return !$this->public;
    }
    /**
     * @param bool $lazy
     * @return static
     */
    public function setLazy($lazy)
    {
        $this->changes['lazy'] = \true;
        $this->lazy = $lazy;
        return $this;
    }
    public function isLazy(): bool
    {
        return $this->lazy;
    }
    /**
     * @param bool $boolean
     * @return static
     */
    public function setSynthetic($boolean)
    {
        $this->synthetic = $boolean;
        if (!isset($this->changes['public'])) {
            $this->setPublic(\true);
        }
        return $this;
    }
    public function isSynthetic(): bool
    {
        return $this->synthetic;
    }
    /**
     * @param bool $boolean
     * @return static
     */
    public function setAbstract($boolean)
    {
        $this->abstract = $boolean;
        return $this;
    }
    public function isAbstract(): bool
    {
        return $this->abstract;
    }
    /**
     * @param string $package
     * @param string $version
     * @param string $message
     * @return static
     */
    public function setDeprecated($package, $version, $message)
    {
        if ('' !== $message) {
            if (preg_match('#[\r\n]|\*/#', $message)) {
                throw new InvalidArgumentException('Invalid characters found in deprecation template.');
            }
            if (strpos($message, '%service_id%') === false) {
                throw new InvalidArgumentException('The deprecation template must contain the "%service_id%" placeholder.');
            }
        }
        $this->changes['deprecated'] = \true;
        $this->deprecation = ['package' => $package, 'version' => $version, 'message' => $message ?: self::DEFAULT_DEPRECATION_TEMPLATE];
        return $this;
    }
    public function isDeprecated(): bool
    {
        return (bool) $this->deprecation;
    }
    /**
     * @param string $id
     */
    public function getDeprecation($id): array
    {
        return ['package' => $this->deprecation['package'], 'version' => $this->deprecation['version'], 'message' => str_replace('%service_id%', $id, $this->deprecation['message'])];
    }
    /**
     * @param string|mixed[]|Reference|null $configurator
     * @return static
     */
    public function setConfigurator($configurator)
    {
        $this->changes['configurator'] = \true;
        if (\is_string($configurator) && strpos($configurator, '::') !== false) {
            $configurator = explode('::', $configurator, 2);
        } elseif ($configurator instanceof Reference) {
            $configurator = [$configurator, '__invoke'];
        }
        $this->configurator = $configurator;
        return $this;
    }
    /**
     * @return mixed[]|string|null
     */
    public function getConfigurator()
    {
        return $this->configurator;
    }
    public function isAutowired(): bool
    {
        return $this->autowired;
    }
    /**
     * @param bool $autowired
     * @return static
     */
    public function setAutowired($autowired)
    {
        $this->changes['autowired'] = \true;
        $this->autowired = $autowired;
        return $this;
    }
    public function getBindings(): array
    {
        return $this->bindings;
    }
    /**
     * @param mixed[] $bindings
     * @return static
     */
    public function setBindings($bindings)
    {
        foreach ($bindings as $key => $binding) {
            if (0 < strpos($key, '$') && $key !== $k = preg_replace('/[ \t]*\$/', ' $', $key)) {
                unset($bindings[$key]);
                $bindings[$key = $k] = $binding;
            }
            if (!$binding instanceof BoundArgument) {
                $bindings[$key] = new BoundArgument($binding);
            }
        }
        $this->bindings = $bindings;
        return $this;
    }
    /**
     * @param string|Closure|\Staatic\Vendor\Symfony\Component\DependencyInjection\Definition $error
     * @return static
     */
    public function addError($error)
    {
        if ($error instanceof self) {
            $this->errors = array_merge($this->errors, $error->errors);
        } else {
            $this->errors[] = $error;
        }
        return $this;
    }
    public function getErrors(): array
    {
        foreach ($this->errors as $i => $error) {
            if ($error instanceof Closure) {
                $this->errors[$i] = (string) $error();
            } elseif (!\is_string($error)) {
                $this->errors[$i] = (string) $error;
            }
        }
        return $this->errors;
    }
    public function hasErrors(): bool
    {
        return (bool) $this->errors;
    }
}
