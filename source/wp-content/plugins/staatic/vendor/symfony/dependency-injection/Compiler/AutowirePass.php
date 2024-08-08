<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use ReflectionParameter;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ArrayObject;
use Closure;
use ReflectionException;
use Staatic\Vendor\Symfony\Component\Config\Resource\ClassExistenceResource;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute\Autowire;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute\MapDecorated;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute\Target;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\AutowiringFailedException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
use Staatic\Vendor\Symfony\Component\DependencyInjection\TypedReference;
use Staatic\Vendor\Symfony\Component\VarExporter\ProxyHelper;
use Staatic\Vendor\Symfony\Contracts\Service\Attribute\SubscribedService;
class AutowirePass extends AbstractRecursivePass
{
    /**
     * @var mixed[]
     */
    private $types;
    /**
     * @var mixed[]
     */
    private $ambiguousServiceTypes;
    /**
     * @var mixed[]
     */
    private $autowiringAliases;
    /**
     * @var string|null
     */
    private $lastFailure;
    /**
     * @var bool
     */
    private $throwOnAutowiringException;
    /**
     * @var string|null
     */
    private $decoratedClass;
    /**
     * @var string|null
     */
    private $decoratedId;
    /**
     * @var mixed[]|null
     */
    private $methodCalls;
    /**
     * @var object
     */
    private $defaultArgument;
    /**
     * @var Closure|null
     */
    private $getPreviousValue;
    /**
     * @var int|null
     */
    private $decoratedMethodIndex;
    /**
     * @var int|null
     */
    private $decoratedMethodArgumentIndex;
    /**
     * @var $this|null
     */
    private $typesClone;
    public function __construct(bool $throwOnAutowireException = \true)
    {
        $this->throwOnAutowiringException = $throwOnAutowireException;
        $this->defaultArgument = new class
        {
            public $value;
            public $names;
            public $bag;
            public function withValue(ReflectionParameter $parameter): self
            {
                $clone = clone $this;
                $clone->value = $this->bag->escapeValue($parameter->getDefaultValue());
                return $clone;
            }
        };
    }
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        $this->defaultArgument->bag = $container->getParameterBag();
        try {
            $this->typesClone = clone $this;
            parent::process($container);
        } finally {
            $this->decoratedClass = null;
            $this->decoratedId = null;
            $this->methodCalls = null;
            $this->defaultArgument->bag = null;
            $this->defaultArgument->names = null;
            $this->getPreviousValue = null;
            $this->decoratedMethodIndex = null;
            $this->decoratedMethodArgumentIndex = null;
            $this->typesClone = null;
        }
    }
    /**
     * @param mixed $value
     * @param bool $isRoot
     * @return mixed
     */
    protected function processValue($value, $isRoot = \false)
    {
        try {
            return $this->doProcessValue($value, $isRoot);
        } catch (AutowiringFailedException $e) {
            if ($this->throwOnAutowiringException) {
                throw $e;
            }
            $this->container->getDefinition($this->currentId)->addError($e->getMessageCallback() ?? $e->getMessage());
            return parent::processValue($value, $isRoot);
        }
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    private function doProcessValue($value, bool $isRoot = \false)
    {
        if ($value instanceof TypedReference) {
            if ($attributes = $value->getAttributes()) {
                $attribute = array_pop($attributes);
                if ($attributes) {
                    throw new AutowiringFailedException($this->currentId, sprintf('Using multiple attributes with "%s" is not supported.', SubscribedService::class));
                }
                if (!$attribute instanceof Target) {
                    return $this->processAttribute($attribute, ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE !== $value->getInvalidBehavior());
                }
                $value = new TypedReference($value->getType(), $value->getType(), $value->getInvalidBehavior(), $attribute->name);
            }
            if ($ref = $this->getAutowiredReference($value, \true)) {
                return $ref;
            }
            if (ContainerBuilder::RUNTIME_EXCEPTION_ON_INVALID_REFERENCE === $value->getInvalidBehavior()) {
                $message = $this->createTypeNotFoundMessageCallback($value, 'it');
                $this->container->register($id = sprintf('.errored.%s.%s', $this->currentId, (string) $value), $value->getType())->addError($message);
                return new TypedReference($id, $value->getType(), $value->getInvalidBehavior(), $value->getName());
            }
        }
        $value = parent::processValue($value, $isRoot);
        if (!$value instanceof Definition || !$value->isAutowired() || $value->isAbstract() || !$value->getClass()) {
            return $value;
        }
        if (!$reflectionClass = $this->container->getReflectionClass($value->getClass(), \false)) {
            $this->container->log($this, sprintf('Skipping service "%s": Class or interface "%s" cannot be loaded.', $this->currentId, $value->getClass()));
            return $value;
        }
        $this->methodCalls = $value->getMethodCalls();
        try {
            $constructor = $this->getConstructor($value, \false);
        } catch (RuntimeException $e) {
            throw new AutowiringFailedException($this->currentId, $e->getMessage(), 0, $e);
        }
        if ($constructor) {
            array_unshift($this->methodCalls, [$constructor, $value->getArguments()]);
        }
        $checkAttributes = !$value->hasTag('container.ignore_attributes');
        $this->methodCalls = $this->autowireCalls($reflectionClass, $isRoot, $checkAttributes);
        if ($constructor) {
            [, $arguments] = array_shift($this->methodCalls);
            if ($arguments !== $value->getArguments()) {
                $value->setArguments($arguments);
            }
        }
        if ($this->methodCalls !== $value->getMethodCalls()) {
            $value->setMethodCalls($this->methodCalls);
        }
        return $value;
    }
    /**
     * @return mixed
     * @param object $attribute
     */
    private function processAttribute($attribute, bool $isOptional = \false)
    {
        switch (\true) {
            case $attribute instanceof Autowire:
                $value = $this->container->getParameterBag()->resolveValue($attribute->value);
                return ($value instanceof Reference && $isOptional) ? new Reference($value, ContainerInterface::NULL_ON_INVALID_REFERENCE) : $value;
            case $attribute instanceof TaggedIterator:
                return new TaggedIteratorArgument($attribute->tag, $attribute->indexAttribute, $attribute->defaultIndexMethod, \false, $attribute->defaultPriorityMethod, (array) $attribute->exclude);
            case $attribute instanceof TaggedLocator:
                return new ServiceLocatorArgument(new TaggedIteratorArgument($attribute->tag, $attribute->indexAttribute, $attribute->defaultIndexMethod, \true, $attribute->defaultPriorityMethod, (array) $attribute->exclude));
            case $attribute instanceof MapDecorated:
                $definition = $this->container->getDefinition($this->currentId);
                return new Reference($definition->innerServiceId ?? $this->currentId . '.inner', $definition->decorationOnInvalid ?? ContainerInterface::NULL_ON_INVALID_REFERENCE);
        }
        throw new AutowiringFailedException($this->currentId, sprintf('"%s" is an unsupported attribute.', get_class($attribute)));
    }
    private function autowireCalls(ReflectionClass $reflectionClass, bool $isRoot, bool $checkAttributes): array
    {
        $this->decoratedId = null;
        $this->decoratedClass = null;
        $this->getPreviousValue = null;
        if ($isRoot && ($definition = $this->container->getDefinition($this->currentId)) && null !== ($this->decoratedId = $definition->innerServiceId) && $this->container->has($this->decoratedId)) {
            $this->decoratedClass = $this->container->findDefinition($this->decoratedId)->getClass();
        }
        $patchedIndexes = [];
        foreach ($this->methodCalls as $i => $call) {
            [$method, $arguments] = $call;
            if ($method instanceof ReflectionFunctionAbstract) {
                $reflectionMethod = $method;
            } else {
                $definition = new Definition($reflectionClass->name);
                try {
                    $reflectionMethod = $this->getReflectionMethod($definition, $method);
                } catch (RuntimeException $e) {
                    if ($definition->getFactory()) {
                        continue;
                    }
                    throw $e;
                }
            }
            $arguments = $this->autowireMethod($reflectionMethod, $arguments, $checkAttributes, $i);
            if ($arguments !== $call[1]) {
                $this->methodCalls[$i][1] = $arguments;
                $patchedIndexes[] = $i;
            }
        }
        foreach ($patchedIndexes as $i) {
            $namedArguments = null;
            $arguments = $this->methodCalls[$i][1];
            foreach ($arguments as $j => $value) {
                if ($namedArguments && !$value instanceof $this->defaultArgument) {
                    unset($arguments[$j]);
                    $arguments[$namedArguments[$j]] = $value;
                }
                if ($namedArguments || !$value instanceof $this->defaultArgument) {
                    continue;
                }
                if (\is_array($value->value) ? $value->value : \is_object($value->value)) {
                    unset($arguments[$j]);
                    $namedArguments = $value->names;
                } else {
                    $arguments[$j] = $value->value;
                }
            }
            $this->methodCalls[$i][1] = $arguments;
        }
        return $this->methodCalls;
    }
    private function autowireMethod(ReflectionFunctionAbstract $reflectionMethod, array $arguments, bool $checkAttributes, int $methodIndex): array
    {
        $class = ($reflectionMethod instanceof ReflectionMethod) ? $reflectionMethod->class : $this->currentId;
        $method = $reflectionMethod->name;
        $parameters = $reflectionMethod->getParameters();
        if ($reflectionMethod->isVariadic()) {
            array_pop($parameters);
        }
        $this->defaultArgument->names = new ArrayObject();
        foreach ($parameters as $index => $parameter) {
            $this->defaultArgument->names[$index] = $parameter->name;
            if (\array_key_exists($parameter->name, $arguments)) {
                $arguments[$index] = $arguments[$parameter->name];
                unset($arguments[$parameter->name]);
            }
            if (\array_key_exists($index, $arguments) && '' !== $arguments[$index]) {
                continue;
            }
            if ($checkAttributes) {
                foreach (method_exists($parameter, 'getAttributes') ? $parameter->getAttributes() : [] as $attribute) {
                    if (\in_array($attribute->getName(), [TaggedIterator::class, TaggedLocator::class, Autowire::class, MapDecorated::class], \true)) {
                        try {
                            $arguments[$index] = $this->processAttribute($attribute->newInstance(), $parameter->allowsNull());
                            continue 2;
                        } catch (ParameterNotFoundException $e) {
                            if (!$parameter->isDefaultValueAvailable()) {
                                throw new AutowiringFailedException($this->currentId, $e->getMessage(), 0, $e);
                            }
                            $arguments[$index] = clone $this->defaultArgument;
                            $arguments[$index]->value = $parameter->getDefaultValue();
                        }
                    }
                }
            }
            if (!$type = ProxyHelper::exportType($parameter, \true)) {
                if (isset($arguments[$index])) {
                    continue;
                }
                if (!$parameter->isDefaultValueAvailable()) {
                    if ($parameter->isOptional()) {
                        --$index;
                        break;
                    }
                    $type = ProxyHelper::exportType($parameter);
                    $type = $type ? sprintf('is type-hinted "%s"', preg_replace('/(^|[(|&])\\\\|^\?\\\\?/', '\1', $type)) : 'has no type-hint';
                    throw new AutowiringFailedException($this->currentId, sprintf('Cannot autowire service "%s": argument "$%s" of method "%s()" %s, you should configure its value explicitly.', $this->currentId, $parameter->name, ($class !== $this->currentId) ? $class . '::' . $method : $method, $type));
                }
                $arguments[$index] = $this->defaultArgument->withValue($parameter);
                continue;
            }
            $getValue = function () use ($type, $parameter, $class, $method) {
                if (!$value = $this->getAutowiredReference($ref = new TypedReference($type, $type, ContainerBuilder::EXCEPTION_ON_INVALID_REFERENCE, Target::parseName($parameter)), \false)) {
                    $failureMessage = $this->createTypeNotFoundMessageCallback($ref, sprintf('argument "$%s" of method "%s()"', $parameter->name, ($class !== $this->currentId) ? $class . '::' . $method : $method));
                    if ($parameter->isDefaultValueAvailable()) {
                        $value = $this->defaultArgument->withValue($parameter);
                    } elseif (!$parameter->allowsNull()) {
                        throw new AutowiringFailedException($this->currentId, $failureMessage);
                    }
                }
                return $value;
            };
            if ($this->decoratedClass && $isDecorated = is_a($this->decoratedClass, $type, \true)) {
                if ($this->getPreviousValue) {
                    $getPreviousValue = $this->getPreviousValue;
                    $this->methodCalls[$this->decoratedMethodIndex][1][$this->decoratedMethodArgumentIndex] = $getPreviousValue();
                    $this->decoratedClass = null;
                } else {
                    $arguments[$index] = new TypedReference($this->decoratedId, $this->decoratedClass);
                    $this->getPreviousValue = $getValue;
                    $this->decoratedMethodIndex = $methodIndex;
                    $this->decoratedMethodArgumentIndex = $index;
                    continue;
                }
            }
            $arguments[$index] = $getValue();
        }
        if ($parameters && !isset($arguments[++$index])) {
            while (0 <= --$index) {
                if (!$arguments[$index] instanceof $this->defaultArgument) {
                    break;
                }
                unset($arguments[$index]);
            }
        }
        ksort($arguments, \SORT_NATURAL);
        return $arguments;
    }
    private function getAutowiredReference(TypedReference $reference, bool $filterType): ?TypedReference
    {
        $this->lastFailure = null;
        $type = $reference->getType();
        if ($type !== (string) $reference) {
            return $reference;
        }
        if ($filterType && \false !== $m = strpbrk($type, '&|')) {
            $types = array_diff(explode($m[0], $type), ['int', 'string', 'array', 'bool', 'float', 'iterable', 'object', 'callable', 'null']);
            sort($types);
            $type = implode($m[0], $types);
        }
        if (null !== $name = $reference->getName()) {
            if ($this->container->has($alias = $type . ' $' . $name) && !$this->container->findDefinition($alias)->isAbstract()) {
                return new TypedReference($alias, $type, $reference->getInvalidBehavior());
            }
            if (null !== ($alias = $this->getCombinedAlias($type, $name) ?? null) && !$this->container->findDefinition($alias)->isAbstract()) {
                return new TypedReference($alias, $type, $reference->getInvalidBehavior());
            }
            if ($this->container->has($name) && !$this->container->findDefinition($name)->isAbstract()) {
                foreach ($this->container->getAliases() as $id => $alias) {
                    if ($name === (string) $alias && strncmp($id, $type . ' $', strlen($type . ' $')) === 0) {
                        return new TypedReference($name, $type, $reference->getInvalidBehavior());
                    }
                }
            }
        }
        if ($this->container->has($type) && !$this->container->findDefinition($type)->isAbstract()) {
            return new TypedReference($type, $type, $reference->getInvalidBehavior());
        }
        if (null !== ($alias = $this->getCombinedAlias($type) ?? null) && !$this->container->findDefinition($alias)->isAbstract()) {
            return new TypedReference($alias, $type, $reference->getInvalidBehavior());
        }
        return null;
    }
    private function populateAvailableTypes(ContainerBuilder $container)
    {
        $this->types = [];
        $this->ambiguousServiceTypes = [];
        $this->autowiringAliases = [];
        foreach ($container->getDefinitions() as $id => $definition) {
            $this->populateAvailableType($container, $id, $definition);
        }
        foreach ($container->getAliases() as $id => $alias) {
            $this->populateAutowiringAlias($id);
        }
    }
    private function populateAvailableType(ContainerBuilder $container, string $id, Definition $definition)
    {
        if ($definition->isAbstract()) {
            return;
        }
        if ('' === $id || '.' === $id[0] || $definition->isDeprecated() || !$reflectionClass = $container->getReflectionClass($definition->getClass(), \false)) {
            return;
        }
        foreach ($reflectionClass->getInterfaces() as $reflectionInterface) {
            $this->set($reflectionInterface->name, $id);
        }
        do {
            $this->set($reflectionClass->name, $id);
        } while ($reflectionClass = $reflectionClass->getParentClass());
        $this->populateAutowiringAlias($id);
    }
    private function set(string $type, string $id)
    {
        if (isset($this->ambiguousServiceTypes[$type])) {
            $this->ambiguousServiceTypes[$type][] = $id;
            return;
        }
        if (!isset($this->types[$type]) || $this->types[$type] === $id) {
            $this->types[$type] = $id;
            return;
        }
        if (!isset($this->ambiguousServiceTypes[$type])) {
            $this->ambiguousServiceTypes[$type] = [$this->types[$type]];
            unset($this->types[$type]);
        }
        $this->ambiguousServiceTypes[$type][] = $id;
    }
    private function createTypeNotFoundMessageCallback(TypedReference $reference, string $label): Closure
    {
        if (null === $this->typesClone->container) {
            $this->typesClone->container = new ContainerBuilder($this->container->getParameterBag());
            $this->typesClone->container->setAliases($this->container->getAliases());
            $this->typesClone->container->setDefinitions($this->container->getDefinitions());
            $this->typesClone->container->setResourceTracking(\false);
        }
        $currentId = $this->currentId;
        return (function () use ($reference, $label, $currentId) {
            return $this->createTypeNotFoundMessage($reference, $label, $currentId);
        })->bindTo($this->typesClone);
    }
    private function createTypeNotFoundMessage(TypedReference $reference, string $label, string $currentId): string
    {
        $type = $reference->getType();
        $i = null;
        $namespace = $type;
        do {
            $namespace = substr($namespace, 0, $i);
            if ($this->container->hasDefinition($namespace) && $tag = $this->container->getDefinition($namespace)->getTag('container.excluded')) {
                return sprintf('Cannot autowire service "%s": %s needs an instance of "%s" but this type has been excluded %s.', $currentId, $label, $type, $tag[0]['source'] ?? 'from autowiring');
            }
        } while (\false !== $i = strrpos($namespace, '\\'));
        if (!$r = $this->container->getReflectionClass($type, \false)) {
            try {
                $resource = new ClassExistenceResource($type, \false);
                $resource->isFresh(0);
                $parentMsg = \false;
            } catch (ReflectionException $e) {
                $parentMsg = $e->getMessage();
            }
            $message = sprintf('has type "%s" but this class %s.', $type, $parentMsg ? sprintf('is missing a parent class (%s)', $parentMsg) : 'was not found');
        } else {
            $alternatives = $this->createTypeAlternatives($this->container, $reference);
            $message = $this->container->has($type) ? 'this service is abstract' : 'no such service exists';
            $message = sprintf('references %s "%s" but %s.%s', $r->isInterface() ? 'interface' : 'class', $type, $message, $alternatives);
            if ($r->isInterface() && !$alternatives) {
                $message .= ' Did you create a class that implements this interface?';
            }
        }
        $message = sprintf('Cannot autowire service "%s": %s %s', $currentId, $label, $message);
        if (null !== $this->lastFailure) {
            $message = $this->lastFailure . "\n" . $message;
            $this->lastFailure = null;
        }
        return $message;
    }
    private function createTypeAlternatives(ContainerBuilder $container, TypedReference $reference): string
    {
        if ($message = $this->getAliasesSuggestionForType($container, $type = $reference->getType())) {
            return ' ' . $message;
        }
        if (!isset($this->ambiguousServiceTypes)) {
            $this->populateAvailableTypes($container);
        }
        $servicesAndAliases = $container->getServiceIds();
        if (null !== ($autowiringAliases = $this->autowiringAliases[$type] ?? null) && !isset($autowiringAliases[''])) {
            return sprintf(' Available autowiring aliases for this %s are: "$%s".', class_exists($type, \false) ? 'class' : 'interface', implode('", "$', $autowiringAliases));
        }
        if (!$container->has($type) && \false !== $key = array_search(strtolower($type), array_map('strtolower', $servicesAndAliases))) {
            return sprintf(' Did you mean "%s"?', $servicesAndAliases[$key]);
        } elseif (isset($this->ambiguousServiceTypes[$type])) {
            $message = sprintf('one of these existing services: "%s"', implode('", "', $this->ambiguousServiceTypes[$type]));
        } elseif (isset($this->types[$type])) {
            $message = sprintf('the existing "%s" service', $this->types[$type]);
        } else {
            return '';
        }
        return sprintf(' You should maybe alias this %s to %s.', class_exists($type, \false) ? 'class' : 'interface', $message);
    }
    private function getAliasesSuggestionForType(ContainerBuilder $container, string $type): ?string
    {
        $aliases = [];
        foreach (class_parents($type) + class_implements($type) as $parent) {
            if ($container->has($parent) && !$container->findDefinition($parent)->isAbstract()) {
                $aliases[] = $parent;
            }
        }
        if (1 < $len = \count($aliases)) {
            $message = 'Try changing the type-hint to one of its parents: ';
            for ($i = 0, --$len; $i < $len; ++$i) {
                $message .= sprintf('%s "%s", ', class_exists($aliases[$i], \false) ? 'class' : 'interface', $aliases[$i]);
            }
            $message .= sprintf('or %s "%s".', class_exists($aliases[$i], \false) ? 'class' : 'interface', $aliases[$i]);
            return $message;
        }
        if ($aliases) {
            return sprintf('Try changing the type-hint to "%s" instead.', $aliases[0]);
        }
        return null;
    }
    private function populateAutowiringAlias(string $id): void
    {
        if (!preg_match('/(?(DEFINE)(?<V>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+))^((?&V)(?:\\\\(?&V))*+)(?: \$((?&V)))?$/', $id, $m)) {
            return;
        }
        $type = $m[2];
        $name = $m[3] ?? '';
        if (class_exists($type, \false) || interface_exists($type, \false)) {
            $this->autowiringAliases[$type][$name] = $name;
        }
    }
    private function getCombinedAlias(string $type, string $name = null): ?string
    {
        if (strpos($type, '&') !== false) {
            $types = explode('&', $type);
        } elseif (strpos($type, '|') !== false) {
            $types = explode('|', $type);
        } else {
            return null;
        }
        $alias = null;
        $suffix = $name ? ' $' . $name : '';
        foreach ($types as $type) {
            if (!$this->container->hasAlias($type . $suffix)) {
                return null;
            }
            if (null === $alias) {
                $alias = (string) $this->container->getAlias($type . $suffix);
            } elseif ((string) $this->container->getAlias($type . $suffix) !== $alias) {
                return null;
            }
        }
        return $alias;
    }
}
