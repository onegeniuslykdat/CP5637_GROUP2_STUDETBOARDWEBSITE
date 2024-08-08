<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
class ResolveEnvPlaceholdersPass extends AbstractRecursivePass
{
    /**
     * @param mixed $value
     * @param bool $isRoot
     * @return mixed
     */
    protected function processValue($value, $isRoot = \false)
    {
        if (\is_string($value)) {
            return $this->container->resolveEnvPlaceholders($value, \true);
        }
        if ($value instanceof Definition) {
            $changes = $value->getChanges();
            if (isset($changes['class'])) {
                $value->setClass($this->container->resolveEnvPlaceholders($value->getClass(), \true));
            }
            if (isset($changes['file'])) {
                $value->setFile($this->container->resolveEnvPlaceholders($value->getFile(), \true));
            }
        }
        $value = parent::processValue($value, $isRoot);
        if ($value && \is_array($value) && !$isRoot) {
            $value = array_combine($this->container->resolveEnvPlaceholders(array_keys($value), \true), $value);
        }
        return $value;
    }
}
