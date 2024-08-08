<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\LogicException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\Expression;
class AnalyzeServiceReferencesPass extends AbstractRecursivePass
{
    /**
     * @var ServiceReferenceGraph
     */
    private $graph;
    /**
     * @var Definition|null
     */
    private $currentDefinition;
    /**
     * @var bool
     */
    private $onlyConstructorArguments;
    /**
     * @var bool
     */
    private $hasProxyDumper;
    /**
     * @var bool
     */
    private $lazy;
    /**
     * @var bool
     */
    private $byConstructor;
    /**
     * @var bool
     */
    private $byFactory;
    /**
     * @var mixed[]
     */
    private $definitions;
    /**
     * @var mixed[]
     */
    private $aliases;
    public function __construct(bool $onlyConstructorArguments = \false, bool $hasProxyDumper = \true)
    {
        $this->onlyConstructorArguments = $onlyConstructorArguments;
        $this->hasProxyDumper = $hasProxyDumper;
        $this->enableExpressionProcessing();
    }
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        $this->container = $container;
        $this->graph = $container->getCompiler()->getServiceReferenceGraph();
        $this->graph->clear();
        $this->lazy = \false;
        $this->byConstructor = \false;
        $this->byFactory = \false;
        $this->definitions = $container->getDefinitions();
        $this->aliases = $container->getAliases();
        foreach ($this->aliases as $id => $alias) {
            $targetId = $this->getDefinitionId((string) $alias);
            $this->graph->connect($id, $alias, $targetId, (null !== $targetId) ? $this->container->getDefinition($targetId) : null, null);
        }
        try {
            parent::process($container);
        } finally {
            $this->aliases = $this->definitions = [];
        }
    }
    /**
     * @param mixed $value
     * @param bool $isRoot
     * @return mixed
     */
    protected function processValue($value, $isRoot = \false)
    {
        $lazy = $this->lazy;
        $inExpression = $this->inExpression();
        if ($value instanceof ArgumentInterface) {
            $this->lazy = !$this->byFactory || !$value instanceof IteratorArgument;
            parent::processValue($value->getValues());
            $this->lazy = $lazy;
            return $value;
        }
        if ($value instanceof Reference) {
            $targetId = $this->getDefinitionId((string) $value);
            $targetDefinition = (null !== $targetId) ? $this->container->getDefinition($targetId) : null;
            $this->graph->connect($this->currentId, $this->currentDefinition, $targetId, $targetDefinition, $value, $this->lazy || $this->hasProxyDumper && (($nullsafeVariable1 = $targetDefinition) ? $nullsafeVariable1->isLazy() : null), ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE === $value->getInvalidBehavior(), $this->byConstructor);
            if ($inExpression) {
                $this->graph->connect('.internal.reference_in_expression', null, $targetId, $targetDefinition, $value, $this->lazy || (($nullsafeVariable2 = $targetDefinition) ? $nullsafeVariable2->isLazy() : null), \true);
            }
            return $value;
        }
        if (!$value instanceof Definition) {
            return parent::processValue($value, $isRoot);
        }
        if ($isRoot) {
            if ($value->isSynthetic() || $value->isAbstract()) {
                return $value;
            }
            $this->currentDefinition = $value;
        } elseif ($this->currentDefinition === $value) {
            return $value;
        }
        $this->lazy = \false;
        $byConstructor = $this->byConstructor;
        $this->byConstructor = $isRoot || $byConstructor;
        $byFactory = $this->byFactory;
        $this->byFactory = \true;
        if (\is_string($factory = $value->getFactory()) && strncmp($factory, '@=', strlen('@=')) === 0) {
            if (!class_exists(Expression::class)) {
                throw new LogicException('Expressions cannot be used in service factories without the ExpressionLanguage component. Try running "composer require symfony/expression-language".');
            }
            $factory = new Expression(substr($factory, 2));
        }
        $this->processValue($factory);
        $this->byFactory = $byFactory;
        $this->processValue($value->getArguments());
        $properties = $value->getProperties();
        $setters = $value->getMethodCalls();
        $lastWitherIndex = null;
        foreach ($setters as $k => $call) {
            if ($call[2] ?? \false) {
                $lastWitherIndex = $k;
            }
        }
        if (null !== $lastWitherIndex) {
            $this->processValue($properties);
            $setters = $properties = [];
            foreach ($value->getMethodCalls() as $k => $call) {
                if (null === $lastWitherIndex) {
                    $setters[] = $call;
                    continue;
                }
                if ($lastWitherIndex === $k) {
                    $lastWitherIndex = null;
                }
                $this->processValue($call);
            }
        }
        $this->byConstructor = $byConstructor;
        if (!$this->onlyConstructorArguments) {
            $this->processValue($properties);
            $this->processValue($setters);
            $this->processValue($value->getConfigurator());
        }
        $this->lazy = $lazy;
        return $value;
    }
    private function getDefinitionId(string $id): ?string
    {
        while (isset($this->aliases[$id])) {
            $id = (string) $this->aliases[$id];
        }
        return isset($this->definitions[$id]) ? $id : null;
    }
}
