<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
use Staatic\Vendor\Symfony\Component\DependencyInjection\TypedReference;
class ResolveInvalidReferencesPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    private $container;
    /**
     * @var RuntimeException
     */
    private $signalingException;
    /**
     * @var string
     */
    private $currentId;
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        $this->container = $container;
        $this->signalingException = new RuntimeException('Invalid reference.');
        try {
            foreach ($container->getDefinitions() as $this->currentId => $definition) {
                $this->processValue($definition);
            }
        } finally {
            unset($this->container, $this->signalingException);
        }
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    private function processValue($value, int $rootLevel = 0, int $level = 0)
    {
        if ($value instanceof ServiceClosureArgument) {
            $value->setValues($this->processValue($value->getValues(), 1, 1));
        } elseif ($value instanceof ArgumentInterface) {
            $value->setValues($this->processValue($value->getValues(), $rootLevel, 1 + $level));
        } elseif ($value instanceof Definition) {
            if ($value->isSynthetic() || $value->isAbstract()) {
                return $value;
            }
            $value->setArguments($this->processValue($value->getArguments(), 0));
            $value->setProperties($this->processValue($value->getProperties(), 1));
            $value->setMethodCalls($this->processValue($value->getMethodCalls(), 2));
        } elseif (\is_array($value)) {
            $i = 0;
            foreach ($value as $k => $v) {
                try {
                    if (\false !== $i && $k !== $i++) {
                        $i = \false;
                    }
                    if ($v !== $processedValue = $this->processValue($v, $rootLevel, 1 + $level)) {
                        $value[$k] = $processedValue;
                    }
                } catch (RuntimeException $e) {
                    if ($rootLevel < $level || $rootLevel && !$level) {
                        unset($value[$k]);
                    } elseif ($rootLevel) {
                        throw $e;
                    } else {
                        $value[$k] = null;
                    }
                }
            }
            if (\false !== $i) {
                $value = array_values($value);
            }
        } elseif ($value instanceof Reference) {
            if ($this->container->hasDefinition($id = (string) $value) ? !$this->container->getDefinition($id)->hasTag('container.excluded') : $this->container->hasAlias($id)) {
                return $value;
            }
            $currentDefinition = $this->container->getDefinition($this->currentId);
            if ($currentDefinition->innerServiceId === $id && ContainerInterface::NULL_ON_INVALID_REFERENCE === $currentDefinition->decorationOnInvalid) {
                return null;
            }
            $invalidBehavior = $value->getInvalidBehavior();
            if (ContainerInterface::RUNTIME_EXCEPTION_ON_INVALID_REFERENCE === $invalidBehavior && $value instanceof TypedReference && !$this->container->has($id)) {
                $e = new ServiceNotFoundException($id, $this->currentId);
                $this->container->register($id = sprintf('.errored.%s.%s', $this->currentId, $id), $value->getType())->addError($e->getMessage());
                return new TypedReference($id, $value->getType(), $value->getInvalidBehavior());
            }
            if (ContainerInterface::NULL_ON_INVALID_REFERENCE === $invalidBehavior) {
                $value = null;
            } elseif (ContainerInterface::IGNORE_ON_INVALID_REFERENCE === $invalidBehavior) {
                if (0 < $level || $rootLevel) {
                    throw $this->signalingException;
                }
                $value = null;
            }
        }
        return $value;
    }
}
