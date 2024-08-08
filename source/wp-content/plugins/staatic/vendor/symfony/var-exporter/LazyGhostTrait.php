<?php

namespace Staatic\Vendor\Symfony\Component\VarExporter;

use ReflectionClass;
use TypeError;
use ReflectionProperty;
use Error;
use Closure;
use Staatic\Vendor\Symfony\Component\Serializer\Attribute\Ignore;
use Staatic\Vendor\Symfony\Component\VarExporter\Internal\Hydrator;
use Staatic\Vendor\Symfony\Component\VarExporter\Internal\LazyObjectRegistry as Registry;
use Staatic\Vendor\Symfony\Component\VarExporter\Internal\LazyObjectState;
use Staatic\Vendor\Symfony\Component\VarExporter\Internal\LazyObjectTrait;
trait LazyGhostTrait
{
    use LazyObjectTrait;
    /**
     * @param Closure|mixed[] $initializer
     * @param mixed[]|null $skippedProperties
     * @param object|null $instance
     * @return static
     */
    public static function createLazyGhost($initializer, $skippedProperties = null, $instance = null)
    {
        if (\is_array($initializer)) {
            trigger_deprecation('symfony/var-exporter', '6.4', 'Per-property lazy-initializers are deprecated and won\'t be supported anymore in 7.0, use an object initializer instead.');
        }
        $onlyProperties = (null === $skippedProperties && \is_array($initializer)) ? $initializer : null;
        if (self::class !== $class = $instance ? get_class($instance) : static::class) {
            $skippedProperties["\x00" . self::class . "\x00lazyObjectState"] = \true;
        } elseif (\defined($class . '::LAZY_OBJECT_PROPERTY_SCOPES')) {
            Hydrator::$propertyScopes[$class] = Hydrator::$propertyScopes[$class] ?? $class::LAZY_OBJECT_PROPERTY_SCOPES;
        }
        $instance = $instance ?? (Registry::$classReflectors[$class] = Registry::$classReflectors[$class] ?? new ReflectionClass($class))->newInstanceWithoutConstructor();
        Registry::$defaultProperties[$class] = Registry::$defaultProperties[$class] ?? (array) $instance;
        $instance->lazyObjectState = new LazyObjectState($initializer, $skippedProperties = $skippedProperties ?? []);
        foreach (Registry::$classResetters[$class] = Registry::$classResetters[$class] ?? Registry::getClassResetters($class) as $reset) {
            $reset($instance, $skippedProperties, $onlyProperties);
        }
        return $instance;
    }
    /**
     * @param bool $partial
     */
    public function isLazyObjectInitialized($partial = \false): bool
    {
        if (!$state = $this->lazyObjectState ?? null) {
            return \true;
        }
        if (!\is_array($state->initializer)) {
            return LazyObjectState::STATUS_INITIALIZED_FULL === $state->status;
        }
        $class = get_class($this);
        $properties = (array) $this;
        if ($partial) {
            return (bool) array_intersect_key($state->initializer, $properties);
        }
        $propertyScopes = Hydrator::$propertyScopes[$class] = Hydrator::$propertyScopes[$class] ?? Hydrator::getPropertyScopes($class);
        foreach ($state->initializer as $key => $initializer) {
            if (!\array_key_exists($key, $properties) && isset($propertyScopes[$key])) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * @return static
     */
    public function initializeLazyObject()
    {
        if (!$state = $this->lazyObjectState ?? null) {
            return $this;
        }
        if (!\is_array($state->initializer)) {
            if (LazyObjectState::STATUS_UNINITIALIZED_FULL === $state->status) {
                $state->initialize($this, '', null);
            }
            return $this;
        }
        $values = isset($state->initializer["\x00"]) ? null : [];
        $class = get_class($this);
        $properties = (array) $this;
        $propertyScopes = Hydrator::$propertyScopes[$class] = Hydrator::$propertyScopes[$class] ?? Hydrator::getPropertyScopes($class);
        foreach ($state->initializer as $key => $initializer) {
            if (\array_key_exists($key, $properties) || ![$scope, $name, $readonlyScope] = $propertyScopes[$key] ?? null) {
                continue;
            }
            $scope = $readonlyScope ?? (('*' !== $scope) ? $scope : $class);
            if (null === $values) {
                if (!\is_array($values = $state->initializer["\x00"]($this, Registry::$defaultProperties[$class]))) {
                    throw new TypeError(sprintf('The lazy-initializer defined for instance of "%s" must return an array, got "%s".', $class, get_debug_type($values)));
                }
                if (\array_key_exists($key, $properties = (array) $this)) {
                    continue;
                }
            }
            if (\array_key_exists($key, $values)) {
                $accessor = Registry::$classAccessors[$scope] = Registry::$classAccessors[$scope] ?? Registry::getClassAccessors($scope);
                $accessor['set']($this, $name, $properties[$key] = $values[$key]);
            } else {
                $state->initialize($this, $name, $scope);
                $properties = (array) $this;
            }
        }
        return $this;
    }
    public function resetLazyObject(): bool
    {
        if (!$state = $this->lazyObjectState ?? null) {
            return \false;
        }
        if (LazyObjectState::STATUS_UNINITIALIZED_FULL !== $state->status) {
            $state->reset($this);
        }
        return \true;
    }
    /**
     * @return mixed
     */
    public function &__get($name)
    {
        $propertyScopes = Hydrator::$propertyScopes[get_class($this)] = Hydrator::$propertyScopes[get_class($this)] ?? Hydrator::getPropertyScopes(get_class($this));
        $scope = null;
        if ([$class, , $readonlyScope] = $propertyScopes[$name] ?? null) {
            $scope = Registry::getScope($propertyScopes, $class, $name);
            $state = $this->lazyObjectState ?? null;
            if ($state && (null === $scope || isset($propertyScopes["\x00{$scope}\x00{$name}"]))) {
                if (LazyObjectState::STATUS_INITIALIZED_FULL === $state->status) {
                    $property = (null === $scope) ? $name : "\x00{$scope}\x00{$name}";
                    $property = $propertyScopes[$property][3] ?? Hydrator::$propertyScopes[get_class($this)][$property][3] = new ReflectionProperty($scope ?? $class, $name);
                } else {
                    $property = null;
                }
                if ((($nullsafeVariable1 = $property) ? $nullsafeVariable1->isInitialized($this) : null) ?? LazyObjectState::STATUS_UNINITIALIZED_PARTIAL !== $state->initialize($this, $name, $readonlyScope ?? $scope)) {
                    goto get_in_scope;
                }
            }
        }
        if ($parent = (Registry::$parentMethods[self::class] = Registry::$parentMethods[self::class] ?? Registry::getParentMethods(self::class))['get']) {
            if (2 === $parent) {
                return parent::__get($name);
            }
            $value = parent::__get($name);
            return $value;
        }
        if (null === $class) {
            $frame = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
            trigger_error(sprintf('Undefined property: %s::$%s in %s on line %s', get_class($this), $name, $frame['file'], $frame['line']), \E_USER_NOTICE);
        }
        get_in_scope:
        try {
            if (null === $scope) {
                if (null === $readonlyScope) {
                    return $this->{$name};
                }
                $value = $this->{$name};
                return $value;
            }
            $accessor = Registry::$classAccessors[$scope] = Registry::$classAccessors[$scope] ?? Registry::getClassAccessors($scope);
            return $accessor['get']($this, $name, null !== $readonlyScope);
        } catch (Error $e) {
            if (Error::class !== get_class($e) || strncmp($e->getMessage(), 'Cannot access uninitialized non-nullable property', strlen('Cannot access uninitialized non-nullable property')) !== 0) {
                throw $e;
            }
            try {
                if (null === $scope) {
                    $this->{$name} = [];
                    return $this->{$name};
                }
                $accessor['set']($this, $name, []);
                return $accessor['get']($this, $name, null !== $readonlyScope);
            } catch (Error $exception) {
                if (preg_match('/^Cannot access uninitialized non-nullable property ([^ ]++) by reference$/', $e->getMessage(), $matches)) {
                    throw new Error('Typed property ' . $matches[1] . ' must not be accessed before initialization', $e->getCode(), $e->getPrevious());
                }
                throw $e;
            }
        }
    }
    public function __set($name, $value): void
    {
        $propertyScopes = Hydrator::$propertyScopes[get_class($this)] = Hydrator::$propertyScopes[get_class($this)] ?? Hydrator::getPropertyScopes(get_class($this));
        $scope = null;
        if ([$class, , $readonlyScope] = $propertyScopes[$name] ?? null) {
            $scope = Registry::getScope($propertyScopes, $class, $name, $readonlyScope);
            $state = $this->lazyObjectState ?? null;
            if ($state && ($readonlyScope === $scope || isset($propertyScopes["\x00{$scope}\x00{$name}"])) && LazyObjectState::STATUS_INITIALIZED_FULL !== $state->status) {
                if (LazyObjectState::STATUS_UNINITIALIZED_FULL === $state->status) {
                    $state->initialize($this, $name, $readonlyScope ?? $scope);
                }
                goto set_in_scope;
            }
        }
        if ((Registry::$parentMethods[self::class] = Registry::$parentMethods[self::class] ?? Registry::getParentMethods(self::class))['set']) {
            parent::__set($name, $value);
            return;
        }
        set_in_scope:
        if (null === $scope) {
            $this->{$name} = $value;
        } else {
            $accessor = Registry::$classAccessors[$scope] = Registry::$classAccessors[$scope] ?? Registry::getClassAccessors($scope);
            $accessor['set']($this, $name, $value);
        }
    }
    public function __isset($name): bool
    {
        $propertyScopes = Hydrator::$propertyScopes[get_class($this)] = Hydrator::$propertyScopes[get_class($this)] ?? Hydrator::getPropertyScopes(get_class($this));
        $scope = null;
        if ([$class, , $readonlyScope] = $propertyScopes[$name] ?? null) {
            $scope = Registry::getScope($propertyScopes, $class, $name);
            $state = $this->lazyObjectState ?? null;
            if ($state && (null === $scope || isset($propertyScopes["\x00{$scope}\x00{$name}"])) && LazyObjectState::STATUS_INITIALIZED_FULL !== $state->status && LazyObjectState::STATUS_UNINITIALIZED_PARTIAL !== $state->initialize($this, $name, $readonlyScope ?? $scope)) {
                goto isset_in_scope;
            }
        }
        if ((Registry::$parentMethods[self::class] = Registry::$parentMethods[self::class] ?? Registry::getParentMethods(self::class))['isset']) {
            return parent::__isset($name);
        }
        isset_in_scope:
        if (null === $scope) {
            return isset($this->{$name});
        }
        $accessor = Registry::$classAccessors[$scope] = Registry::$classAccessors[$scope] ?? Registry::getClassAccessors($scope);
        return $accessor['isset']($this, $name);
    }
    public function __unset($name): void
    {
        $propertyScopes = Hydrator::$propertyScopes[get_class($this)] = Hydrator::$propertyScopes[get_class($this)] ?? Hydrator::getPropertyScopes(get_class($this));
        $scope = null;
        if ([$class, , $readonlyScope] = $propertyScopes[$name] ?? null) {
            $scope = Registry::getScope($propertyScopes, $class, $name, $readonlyScope);
            $state = $this->lazyObjectState ?? null;
            if ($state && ($readonlyScope === $scope || isset($propertyScopes["\x00{$scope}\x00{$name}"])) && LazyObjectState::STATUS_INITIALIZED_FULL !== $state->status) {
                if (LazyObjectState::STATUS_UNINITIALIZED_FULL === $state->status) {
                    $state->initialize($this, $name, $readonlyScope ?? $scope);
                }
                goto unset_in_scope;
            }
        }
        if ((Registry::$parentMethods[self::class] = Registry::$parentMethods[self::class] ?? Registry::getParentMethods(self::class))['unset']) {
            parent::__unset($name);
            return;
        }
        unset_in_scope:
        if (null === $scope) {
            unset($this->{$name});
        } else {
            $accessor = Registry::$classAccessors[$scope] = Registry::$classAccessors[$scope] ?? Registry::getClassAccessors($scope);
            $accessor['unset']($this, $name);
        }
    }
    public function __clone()
    {
        if ($state = $this->lazyObjectState ?? null) {
            $this->lazyObjectState = clone $state;
        }
        if ((Registry::$parentMethods[self::class] = Registry::$parentMethods[self::class] ?? Registry::getParentMethods(self::class))['clone']) {
            parent::__clone();
        }
    }
    public function __serialize(): array
    {
        $class = self::class;
        if ((Registry::$parentMethods[$class] = Registry::$parentMethods[$class] ?? Registry::getParentMethods($class))['serialize']) {
            $properties = parent::__serialize();
        } else {
            $this->initializeLazyObject();
            $properties = (array) $this;
        }
        unset($properties["\x00{$class}\x00lazyObjectState"]);
        if (Registry::$parentMethods[$class]['serialize'] || !Registry::$parentMethods[$class]['sleep']) {
            return $properties;
        }
        $scope = get_parent_class($class);
        $data = [];
        foreach (parent::__sleep() as $name) {
            $value = $properties[$k = $name] ?? $properties[$k = "\x00*\x00{$name}"] ?? $properties[$k = "\x00{$class}\x00{$name}"] ?? $properties[$k = "\x00{$scope}\x00{$name}"] ?? $k = null;
            if (null === $k) {
                trigger_error(sprintf('serialize(): "%s" returned as member variable from __sleep() but does not exist', $name), \E_USER_NOTICE);
            } else {
                $data[$k] = $value;
            }
        }
        return $data;
    }
    public function __destruct()
    {
        $state = $this->lazyObjectState ?? null;
        if ($state && \in_array($state->status, [LazyObjectState::STATUS_UNINITIALIZED_FULL, LazyObjectState::STATUS_UNINITIALIZED_PARTIAL], \true)) {
            return;
        }
        if ((Registry::$parentMethods[self::class] = Registry::$parentMethods[self::class] ?? Registry::getParentMethods(self::class))['destruct']) {
            parent::__destruct();
        }
    }
    private function setLazyObjectAsInitialized(bool $initialized): void
    {
        $state = $this->lazyObjectState ?? null;
        if ($state && !\is_array($state->initializer)) {
            $state->status = $initialized ? LazyObjectState::STATUS_INITIALIZED_FULL : LazyObjectState::STATUS_UNINITIALIZED_FULL;
        }
    }
}
