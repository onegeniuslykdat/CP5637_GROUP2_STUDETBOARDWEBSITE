<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class DefinitionErrorExceptionPass extends AbstractRecursivePass
{
    private $erroredDefinitions = [];
    private $sourceReferences = [];
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        try {
            parent::process($container);
            $visitedIds = [];
            foreach ($this->erroredDefinitions as $id => $definition) {
                if ($this->isErrorForRuntime($id, $visitedIds)) {
                    continue;
                }
                $errors = $definition->getErrors();
                throw new RuntimeException(reset($errors));
            }
        } finally {
            $this->erroredDefinitions = [];
            $this->sourceReferences = [];
        }
    }
    /**
     * @param mixed $value
     * @param bool $isRoot
     * @return mixed
     */
    protected function processValue($value, $isRoot = \false)
    {
        if ($value instanceof ArgumentInterface) {
            parent::processValue($value->getValues());
            return $value;
        }
        if ($value instanceof Reference && $this->currentId !== $targetId = (string) $value) {
            if (ContainerInterface::RUNTIME_EXCEPTION_ON_INVALID_REFERENCE === $value->getInvalidBehavior()) {
                $this->sourceReferences[$targetId][$this->currentId] = $this->sourceReferences[$targetId][$this->currentId] ?? \true;
            } else {
                $this->sourceReferences[$targetId][$this->currentId] = \false;
            }
            return $value;
        }
        if (!$value instanceof Definition || !$value->hasErrors()) {
            return parent::processValue($value, $isRoot);
        }
        $this->erroredDefinitions[$this->currentId] = $value;
        return parent::processValue($value);
    }
    private function isErrorForRuntime(string $id, array &$visitedIds): bool
    {
        if (!isset($this->sourceReferences[$id])) {
            return \false;
        }
        if (isset($visitedIds[$id])) {
            return $visitedIds[$id];
        }
        $visitedIds[$id] = \true;
        foreach ($this->sourceReferences[$id] as $sourceId => $isRuntime) {
            if ($visitedIds[$sourceId] ?? $visitedIds[$sourceId] = $this->isErrorForRuntime($sourceId, $visitedIds)) {
                continue;
            }
            if (!$isRuntime) {
                return \false;
            }
        }
        return \true;
    }
}
