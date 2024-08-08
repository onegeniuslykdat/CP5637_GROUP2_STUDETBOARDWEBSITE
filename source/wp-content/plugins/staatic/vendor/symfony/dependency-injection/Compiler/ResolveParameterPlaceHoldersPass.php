<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
class ResolveParameterPlaceHoldersPass extends AbstractRecursivePass
{
    /**
     * @var bool
     */
    private $resolveArrays = \true;
    /**
     * @var bool
     */
    private $throwOnResolveException = \true;
    /**
     * @var ParameterBagInterface
     */
    private $bag;
    public function __construct(bool $resolveArrays = \true, bool $throwOnResolveException = \true)
    {
        $this->resolveArrays = $resolveArrays;
        $this->throwOnResolveException = $throwOnResolveException;
    }
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        $this->bag = $container->getParameterBag();
        try {
            parent::process($container);
            $aliases = [];
            foreach ($container->getAliases() as $name => $target) {
                $this->currentId = $name;
                $aliases[$this->bag->resolveValue($name)] = $target;
            }
            $container->setAliases($aliases);
        } catch (ParameterNotFoundException $e) {
            $e->setSourceId($this->currentId);
            throw $e;
        }
        $this->bag->resolve();
        unset($this->bag);
    }
    /**
     * @param mixed $value
     * @param bool $isRoot
     * @return mixed
     */
    protected function processValue($value, $isRoot = \false)
    {
        if (\is_string($value)) {
            try {
                $v = $this->bag->resolveValue($value);
            } catch (ParameterNotFoundException $e) {
                if ($this->throwOnResolveException) {
                    throw $e;
                }
                $v = null;
                $this->container->getDefinition($this->currentId)->addError($e->getMessage());
            }
            return ($this->resolveArrays || !$v || !\is_array($v)) ? $v : $value;
        }
        if ($value instanceof Definition) {
            $value->setBindings($this->processValue($value->getBindings()));
            $changes = $value->getChanges();
            if (isset($changes['class'])) {
                $value->setClass($this->bag->resolveValue($value->getClass()));
            }
            if (isset($changes['file'])) {
                $value->setFile($this->bag->resolveValue($value->getFile()));
            }
            $tags = $value->getTags();
            if (isset($tags['proxy'])) {
                $tags['proxy'] = $this->bag->resolveValue($tags['proxy']);
                $value->setTags($tags);
            }
        }
        $value = parent::processValue($value, $isRoot);
        if ($value && \is_array($value)) {
            $value = array_combine($this->bag->resolveValue(array_keys($value)), $value);
        }
        return $value;
    }
}
