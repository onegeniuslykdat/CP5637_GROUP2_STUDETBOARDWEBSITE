<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use Staatic\Vendor\Symfony\Component\Config\Definition\BaseNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Staatic\Vendor\Symfony\Component\Config\Definition\NodeInterface;
abstract class NodeDefinition implements NodeParentInterface
{
    protected $name;
    protected $normalization;
    protected $validation;
    protected $defaultValue;
    protected $default = \false;
    protected $required = \false;
    protected $deprecation = [];
    protected $merge;
    protected $allowEmptyValue = \true;
    protected $nullEquivalent;
    protected $trueEquivalent = \true;
    protected $falseEquivalent = \false;
    protected $pathSeparator = BaseNode::DEFAULT_PATH_SEPARATOR;
    protected $parent;
    protected $attributes = [];
    public function __construct(?string $name, ?NodeParentInterface $parent = null)
    {
        $this->parent = $parent;
        $this->name = $name;
    }
    /**
     * @param NodeParentInterface $parent
     * @return static
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }
    /**
     * @param string $info
     * @return static
     */
    public function info($info)
    {
        return $this->attribute('info', $info);
    }
    /**
     * @param string|mixed[] $example
     * @return static
     */
    public function example($example)
    {
        return $this->attribute('example', $example);
    }
    /**
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function attribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }
    /**
     * @return NodeParentInterface|NodeBuilder|$this|ArrayNodeDefinition|VariableNodeDefinition|null
     */
    public function end()
    {
        return $this->parent;
    }
    /**
     * @param bool $forceRootNode
     */
    public function getNode($forceRootNode = \false): NodeInterface
    {
        if ($forceRootNode) {
            $this->parent = null;
        }
        if (isset($this->normalization)) {
            $allowedTypes = [];
            foreach ($this->normalization->before as $expr) {
                $allowedTypes[] = $expr->allowedTypes;
            }
            $allowedTypes = array_unique($allowedTypes);
            $this->normalization->before = ExprBuilder::buildExpressions($this->normalization->before);
            $this->normalization->declaredTypes = $allowedTypes;
        }
        if (isset($this->validation)) {
            $this->validation->rules = ExprBuilder::buildExpressions($this->validation->rules);
        }
        $node = $this->createNode();
        if ($node instanceof BaseNode) {
            $node->setAttributes($this->attributes);
        }
        return $node;
    }
    /**
     * @param mixed $value
     * @return static
     */
    public function defaultValue($value)
    {
        $this->default = \true;
        $this->defaultValue = $value;
        return $this;
    }
    /**
     * @return static
     */
    public function isRequired()
    {
        $this->required = \true;
        return $this;
    }
    /**
     * @param string $package
     * @param string $version
     * @param string $message
     * @return static
     */
    public function setDeprecated($package, $version, $message = 'The child node "%node%" at path "%path%" is deprecated.')
    {
        $this->deprecation = ['package' => $package, 'version' => $version, 'message' => $message];
        return $this;
    }
    /**
     * @param mixed $value
     * @return static
     */
    public function treatNullLike($value)
    {
        $this->nullEquivalent = $value;
        return $this;
    }
    /**
     * @param mixed $value
     * @return static
     */
    public function treatTrueLike($value)
    {
        $this->trueEquivalent = $value;
        return $this;
    }
    /**
     * @param mixed $value
     * @return static
     */
    public function treatFalseLike($value)
    {
        $this->falseEquivalent = $value;
        return $this;
    }
    /**
     * @return static
     */
    public function defaultNull()
    {
        return $this->defaultValue(null);
    }
    /**
     * @return static
     */
    public function defaultTrue()
    {
        return $this->defaultValue(\true);
    }
    /**
     * @return static
     */
    public function defaultFalse()
    {
        return $this->defaultValue(\false);
    }
    public function beforeNormalization(): ExprBuilder
    {
        return $this->normalization()->before();
    }
    /**
     * @return static
     */
    public function cannotBeEmpty()
    {
        $this->allowEmptyValue = \false;
        return $this;
    }
    public function validate(): ExprBuilder
    {
        return $this->validation()->rule();
    }
    /**
     * @param bool $deny
     * @return static
     */
    public function cannotBeOverwritten($deny = \true)
    {
        $this->merge()->denyOverwrite($deny);
        return $this;
    }
    protected function validation(): ValidationBuilder
    {
        return $this->validation = $this->validation ?? new ValidationBuilder($this);
    }
    protected function merge(): MergeBuilder
    {
        return $this->merge = $this->merge ?? new MergeBuilder($this);
    }
    protected function normalization(): NormalizationBuilder
    {
        return $this->normalization = $this->normalization ?? new NormalizationBuilder($this);
    }
    abstract protected function createNode(): NodeInterface;
    /**
     * @param string $separator
     * @return static
     */
    public function setPathSeparator($separator)
    {
        if ($this instanceof ParentNodeDefinitionInterface) {
            foreach ($this->getChildNodeDefinitions() as $child) {
                $child->setPathSeparator($separator);
            }
        }
        $this->pathSeparator = $separator;
        return $this;
    }
}
