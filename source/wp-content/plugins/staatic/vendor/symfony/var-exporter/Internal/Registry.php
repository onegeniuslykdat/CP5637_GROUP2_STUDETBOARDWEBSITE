<?php

namespace Staatic\Vendor\Symfony\Component\VarExporter\Internal;

use Closure;
use ReflectionClass;
use ReflectionException;
use Exception;
use Throwable;
use Serializable;
use Reflector;
use ReflectionGenerator;
use ReflectionType;
use IteratorIterator;
use RecursiveIteratorIterator;
use ReflectionProperty;
use Error;
use Staatic\Vendor\Symfony\Component\VarExporter\Exception\ClassNotFoundException;
use Staatic\Vendor\Symfony\Component\VarExporter\Exception\NotInstantiableTypeException;
class Registry
{
    /**
     * @var mixed[]
     */
    public static $reflectors = [];
    /**
     * @var mixed[]
     */
    public static $prototypes = [];
    /**
     * @var mixed[]
     */
    public static $factories = [];
    /**
     * @var mixed[]
     */
    public static $cloneable = [];
    /**
     * @var mixed[]
     */
    public static $instantiableWithoutConstructor = [];
    public $classes = [];
    public function __construct(array $classes)
    {
        $this->classes = $classes;
    }
    public static function unserialize($objects, $serializables)
    {
        $unserializeCallback = ini_set('unserialize_callback_func', __CLASS__ . '::getClassReflector');
        try {
            foreach ($serializables as $k => $v) {
                $objects[$k] = unserialize($v);
            }
        } finally {
            ini_set('unserialize_callback_func', $unserializeCallback);
        }
        return $objects;
    }
    public static function p($class)
    {
        self::getClassReflector($class, \true, \true);
        return self::$prototypes[$class];
    }
    public static function f($class)
    {
        $reflector = self::$reflectors[$class] = self::$reflectors[$class] ?? self::getClassReflector($class, \true, \false);
        return self::$factories[$class] = Closure::fromCallable([$reflector, 'newInstanceWithoutConstructor']);
    }
    public static function getClassReflector($class, $instantiableWithoutConstructor = \false, $cloneable = null)
    {
        if (!($isClass = class_exists($class)) && !interface_exists($class, \false) && !trait_exists($class, \false)) {
            throw new ClassNotFoundException($class);
        }
        $reflector = new ReflectionClass($class);
        if ($instantiableWithoutConstructor) {
            $proto = $reflector->newInstanceWithoutConstructor();
        } elseif (!$isClass || $reflector->isAbstract()) {
            throw new NotInstantiableTypeException($class);
        } elseif ($reflector->name !== $class) {
            $reflector = self::$reflectors[$name = $reflector->name] = self::$reflectors[$name = $reflector->name] ?? self::getClassReflector($name, \false, $cloneable);
            self::$cloneable[$class] = self::$cloneable[$name];
            self::$instantiableWithoutConstructor[$class] = self::$instantiableWithoutConstructor[$name];
            self::$prototypes[$class] = self::$prototypes[$name];
            return $reflector;
        } else {
            try {
                $proto = $reflector->newInstanceWithoutConstructor();
                $instantiableWithoutConstructor = \true;
            } catch (ReflectionException $exception) {
                $proto = ($reflector->implementsInterface('Serializable') && !method_exists($class, '__unserialize')) ? 'C:' : 'O:';
                if ('C:' === $proto && !$reflector->getMethod('unserialize')->isInternal()) {
                    $proto = null;
                } else {
                    try {
                        $proto = @unserialize($proto . \strlen($class) . ':"' . $class . '":0:{}');
                    } catch (Exception $e) {
                        if (__FILE__ !== $e->getFile()) {
                            throw $e;
                        }
                        throw new NotInstantiableTypeException($class, $e);
                    }
                    if (\false === $proto) {
                        throw new NotInstantiableTypeException($class);
                    }
                }
            }
            if (null !== $proto && !$proto instanceof Throwable && !$proto instanceof Serializable && !method_exists($class, '__sleep') && !method_exists($class, '__serialize')) {
                try {
                    serialize($proto);
                } catch (Exception $e) {
                    throw new NotInstantiableTypeException($class, $e);
                }
            }
        }
        if (null === $cloneable) {
            if (($proto instanceof Reflector || $proto instanceof ReflectionGenerator || $proto instanceof ReflectionType || $proto instanceof IteratorIterator || $proto instanceof RecursiveIteratorIterator) && (!$proto instanceof Serializable && !method_exists($proto, '__wakeup') && !method_exists($class, '__unserialize'))) {
                throw new NotInstantiableTypeException($class);
            }
            $cloneable = $reflector->isCloneable() && !$reflector->hasMethod('__clone');
        }
        self::$cloneable[$class] = $cloneable;
        self::$instantiableWithoutConstructor[$class] = $instantiableWithoutConstructor;
        self::$prototypes[$class] = $proto;
        if ($proto instanceof Throwable) {
            static $setTrace;
            if (null === $setTrace) {
                $setTrace = [new ReflectionProperty(Error::class, 'trace'), new ReflectionProperty(Exception::class, 'trace')];
                $setTrace[0] = Closure::fromCallable([$setTrace[0], 'setValue']);
                $setTrace[1] = Closure::fromCallable([$setTrace[1], 'setValue']);
            }
            $setTrace[$proto instanceof Exception]($proto, []);
        }
        return $reflector;
    }
}
