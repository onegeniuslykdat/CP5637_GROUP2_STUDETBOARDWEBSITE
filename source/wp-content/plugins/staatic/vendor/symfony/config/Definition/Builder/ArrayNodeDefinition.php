<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use InvalidArgumentException;
use RuntimeException;
use Staatic\Vendor\Symfony\Component\Config\Definition\ArrayNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Staatic\Vendor\Symfony\Component\Config\Definition\NodeInterface;
use Staatic\Vendor\Symfony\Component\Config\Definition\PrototypedArrayNode;
class ArrayNodeDefinition extends NodeDefinition implements ParentNodeDefinitionInterface
{
    protected $performDeepMerging = \true;
    protected $ignoreExtraKeys = \false;
    protected $removeExtraKeys = \true;
    protected $children = [];
    protected $prototype;
    protected $atLeastOne = \false;
    protected $allowNewKeys = \true;
    protected $key;
    protected $removeKeyItem;
    protected $addDefaults = \false;
    protected $addDefaultChildren = \false;
    protected $nodeBuilder;
    protected $normalizeKeys = \true;
    public function __construct(?string $name, ?NodeParentInterface $parent = null)
    {
        parent::__construct($name, $parent);
        $this->nullEquivalent = [];
        $this->trueEquivalent = [];
    }
    /**
     * @param NodeBuilder $builder
     */
    public function setBuilder($builder)
    {
        $this->nodeBuilder = $builder;
    }
    public function children(): NodeBuilder
    {
        return $this->getNodeBuilder();
    }
    /**
     * @param string $type
     */
    public function prototype($type): NodeDefinition
    {
        return $this->prototype = $this->getNodeBuilder()->node(null, $type)->setParent($this);
    }
    public function variablePrototype(): VariableNodeDefinition
    {
        return $this->prototype('variable');
    }
    public function scalarPrototype(): ScalarNodeDefinition
    {
        return $this->prototype('scalar');
    }
    public function booleanPrototype(): BooleanNodeDefinition
    {
        return $this->prototype('boolean');
    }
    public function integerPrototype(): IntegerNodeDefinition
    {
        return $this->prototype('integer');
    }
    public function floatPrototype(): FloatNodeDefinition
    {
        return $this->prototype('float');
    }
    public function arrayPrototype(): self
    {
        return $this->prototype('array');
    }
    public function enumPrototype(): EnumNodeDefinition
    {
        return $this->prototype('enum');
    }
    /**
     * @return static
     */
    public function addDefaultsIfNotSet()
    {
        $this->addDefaults = \true;
        return $this;
    }
    /**
     * @param int|string|mixed[]|null $children
     * @return static
     */
    public function addDefaultChildrenIfNoneSet($children = null)
    {
        $this->addDefaultChildren = $children;
        return $this;
    }
    /**
     * @return static
     */
    public function requiresAtLeastOneElement()
    {
        $this->atLeastOne = \true;
        return $this;
    }
    /**
     * @return static
     */
    public function disallowNewKeysInSubsequentConfigs()
    {
        $this->allowNewKeys = \false;
        return $this;
    }
    /**
     * @param string $singular
     * @param string|null $plural
     * @return static
     */
    public function fixXmlConfig($singular, $plural = null)
    {
        $this->normalization()->remap($singular, $plural);
        return $this;
    }
    /**
     * @param string $name
     * @param bool $removeKeyItem
     * @return static
     */
    public function useAttributeAsKey($name, $removeKeyItem = \true)
    {
        $this->key = $name;
        $this->removeKeyItem = $removeKeyItem;
        return $this;
    }
    /**
     * @param bool $allow
     * @return static
     */
    public function canBeUnset($allow = \true)
    {
        $this->merge()->allowUnset($allow);
        return $this;
    }
    /**
     * @return static
     */
    public function canBeEnabled()
    {
        $this->addDefaultsIfNotSet()->treatFalseLike(['enabled' => \false])->treatTrueLike(['enabled' => \true])->treatNullLike(['enabled' => \true])->beforeNormalization()->ifArray()->then(function (array $v) {
            $v['enabled'] = $v['enabled'] ?? \true;
            return $v;
        })->end()->children()->booleanNode('enabled')->defaultFalse();
        return $this;
    }
    /**
     * @return static
     */
    public function canBeDisabled()
    {
        $this->addDefaultsIfNotSet()->treatFalseLike(['enabled' => \false])->treatTrueLike(['enabled' => \true])->treatNullLike(['enabled' => \true])->children()->booleanNode('enabled')->defaultTrue();
        return $this;
    }
    /**
     * @return static
     */
    public function performNoDeepMerging()
    {
        $this->performDeepMerging = \false;
        return $this;
    }
    /**
     * @param bool $remove
     * @return static
     */
    public function ignoreExtraKeys($remove = \true)
    {
        $this->ignoreExtraKeys = \true;
        $this->removeExtraKeys = $remove;
        return $this;
    }
    /**
     * @param bool $bool
     * @return static
     */
    public function normalizeKeys($bool)
    {
        $this->normalizeKeys = $bool;
        return $this;
    }
    /**
     * @param NodeDefinition $node
     * @return static
     */
    public function append($node)
    {
        $this->children[$node->name] = $node->setParent($this);
        return $this;
    }
    protected function getNodeBuilder(): NodeBuilder
    {
        $this->nodeBuilder = $this->nodeBuilder ?? new NodeBuilder();
        return $this->nodeBuilder->setParent($this);
    }
    protected function createNode(): NodeInterface
    {
        if (!isset($this->prototype)) {
            $node = new ArrayNode($this->name, $this->parent, $this->pathSeparator);
            $this->validateConcreteNode($node);
            $node->setAddIfNotSet($this->addDefaults);
            foreach ($this->children as $child) {
                $child->parent = $node;
                $node->addChild($child->getNode());
            }
        } else {
            $node = new PrototypedArrayNode($this->name, $this->parent, $this->pathSeparator);
            $this->validatePrototypeNode($node);
            if (null !== $this->key) {
                $node->setKeyAttribute($this->key, $this->removeKeyItem);
            }
            if (\true === $this->atLeastOne || \false === $this->allowEmptyValue) {
                $node->setMinNumberOfElements(1);
            }
            if ($this->default) {
                if (!\is_array($this->defaultValue)) {
                    throw new InvalidArgumentException(sprintf('%s: the default value of an array node has to be an array.', $node->getPath()));
                }
                $node->setDefaultValue($this->defaultValue);
            }
            if (\false !== $this->addDefaultChildren) {
                $node->setAddChildrenIfNoneSet($this->addDefaultChildren);
                if ($this->prototype instanceof static && !isset($this->prototype->prototype)) {
                    $this->prototype->addDefaultsIfNotSet();
                }
            }
            $this->prototype->parent = $node;
            $node->setPrototype($this->prototype->getNode());
        }
        $node->setAllowNewKeys($this->allowNewKeys);
        $node->addEquivalentValue(null, $this->nullEquivalent);
        $node->addEquivalentValue(\true, $this->trueEquivalent);
        $node->addEquivalentValue(\false, $this->falseEquivalent);
        $node->setPerformDeepMerging($this->performDeepMerging);
        $node->setRequired($this->required);
        $node->setIgnoreExtraKeys($this->ignoreExtraKeys, $this->removeExtraKeys);
        $node->setNormalizeKeys($this->normalizeKeys);
        if ($this->deprecation) {
            $node->setDeprecated($this->deprecation['package'], $this->deprecation['version'], $this->deprecation['message']);
        }
        if (isset($this->normalization)) {
            $node->setNormalizationClosures($this->normalization->before);
            $node->setNormalizedTypes($this->normalization->declaredTypes);
            $node->setXmlRemappings($this->normalization->remappings);
        }
        if (isset($this->merge)) {
            $node->setAllowOverwrite($this->merge->allowOverwrite);
            $node->setAllowFalse($this->merge->allowFalse);
        }
        if (isset($this->validation)) {
            $node->setFinalValidationClosures($this->validation->rules);
        }
        return $node;
    }
    /**
     * @param ArrayNode $node
     */
    protected function validateConcreteNode($node)
    {
        $path = $node->getPath();
        if (null !== $this->key) {
            throw new InvalidDefinitionException(sprintf('->useAttributeAsKey() is not applicable to concrete nodes at path "%s".', $path));
        }
        if (\false === $this->allowEmptyValue) {
            throw new InvalidDefinitionException(sprintf('->cannotBeEmpty() is not applicable to concrete nodes at path "%s".', $path));
        }
        if (\true === $this->atLeastOne) {
            throw new InvalidDefinitionException(sprintf('->requiresAtLeastOneElement() is not applicable to concrete nodes at path "%s".', $path));
        }
        if ($this->default) {
            throw new InvalidDefinitionException(sprintf('->defaultValue() is not applicable to concrete nodes at path "%s".', $path));
        }
        if (\false !== $this->addDefaultChildren) {
            throw new InvalidDefinitionException(sprintf('->addDefaultChildrenIfNoneSet() is not applicable to concrete nodes at path "%s".', $path));
        }
    }
    /**
     * @param PrototypedArrayNode $node
     */
    protected function validatePrototypeNode($node)
    {
        $path = $node->getPath();
        if ($this->addDefaults) {
            throw new InvalidDefinitionException(sprintf('->addDefaultsIfNotSet() is not applicable to prototype nodes at path "%s".', $path));
        }
        if (\false !== $this->addDefaultChildren) {
            if ($this->default) {
                throw new InvalidDefinitionException(sprintf('A default value and default children might not be used together at path "%s".', $path));
            }
            if (null !== $this->key && (null === $this->addDefaultChildren || \is_int($this->addDefaultChildren) && $this->addDefaultChildren > 0)) {
                throw new InvalidDefinitionException(sprintf('->addDefaultChildrenIfNoneSet() should set default children names as ->useAttributeAsKey() is used at path "%s".', $path));
            }
            if (null === $this->key && (\is_string($this->addDefaultChildren) || \is_array($this->addDefaultChildren))) {
                throw new InvalidDefinitionException(sprintf('->addDefaultChildrenIfNoneSet() might not set default children names as ->useAttributeAsKey() is not used at path "%s".', $path));
            }
        }
    }
    public function getChildNodeDefinitions(): array
    {
        return $this->children;
    }
    /**
     * @param string $nodePath
     */
    public function find($nodePath): NodeDefinition
    {
        $firstPathSegment = (\false === $pathSeparatorPos = strpos($nodePath, $this->pathSeparator)) ? $nodePath : substr($nodePath, 0, $pathSeparatorPos);
        if (null === $node = $this->children[$firstPathSegment] ?? null) {
            throw new RuntimeException(sprintf('Node with name "%s" does not exist in the current node "%s".', $firstPathSegment, $this->name));
        }
        if (\false === $pathSeparatorPos) {
            return $node;
        }
        return $node->find(substr($nodePath, $pathSeparatorPos + \strlen($this->pathSeparator)));
    }
}
