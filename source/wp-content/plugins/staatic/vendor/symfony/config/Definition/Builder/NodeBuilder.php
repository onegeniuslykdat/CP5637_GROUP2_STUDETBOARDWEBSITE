<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use RuntimeException;
class NodeBuilder implements NodeParentInterface
{
    protected $parent;
    protected $nodeMapping;
    public function __construct()
    {
        $this->nodeMapping = ['variable' => VariableNodeDefinition::class, 'scalar' => ScalarNodeDefinition::class, 'boolean' => BooleanNodeDefinition::class, 'integer' => IntegerNodeDefinition::class, 'float' => FloatNodeDefinition::class, 'array' => ArrayNodeDefinition::class, 'enum' => EnumNodeDefinition::class];
    }
    /**
     * @param ParentNodeDefinitionInterface|null $parent
     * @return static
     */
    public function setParent($parent = null)
    {
        if (1 > \func_num_args()) {
            trigger_deprecation('symfony/form', '6.2', 'Calling "%s()" without any arguments is deprecated, pass null explicitly instead.', __METHOD__);
        }
        $this->parent = $parent;
        return $this;
    }
    /**
     * @param string $name
     */
    public function arrayNode($name): ArrayNodeDefinition
    {
        return $this->node($name, 'array');
    }
    /**
     * @param string $name
     */
    public function scalarNode($name): ScalarNodeDefinition
    {
        return $this->node($name, 'scalar');
    }
    /**
     * @param string $name
     */
    public function booleanNode($name): BooleanNodeDefinition
    {
        return $this->node($name, 'boolean');
    }
    /**
     * @param string $name
     */
    public function integerNode($name): IntegerNodeDefinition
    {
        return $this->node($name, 'integer');
    }
    /**
     * @param string $name
     */
    public function floatNode($name): FloatNodeDefinition
    {
        return $this->node($name, 'float');
    }
    /**
     * @param string $name
     */
    public function enumNode($name): EnumNodeDefinition
    {
        return $this->node($name, 'enum');
    }
    /**
     * @param string $name
     */
    public function variableNode($name): VariableNodeDefinition
    {
        return $this->node($name, 'variable');
    }
    public function end()
    {
        return $this->parent;
    }
    /**
     * @param string|null $name
     * @param string $type
     */
    public function node($name, $type): NodeDefinition
    {
        $class = $this->getNodeClass($type);
        $node = new $class($name);
        $this->append($node);
        return $node;
    }
    /**
     * @param NodeDefinition $node
     * @return static
     */
    public function append($node)
    {
        if ($node instanceof BuilderAwareInterface) {
            $builder = clone $this;
            $builder->setParent(null);
            $node->setBuilder($builder);
        }
        if (null !== $this->parent) {
            $this->parent->append($node);
            $node->setParent($this);
        }
        return $this;
    }
    /**
     * @param string $type
     * @param string $class
     * @return static
     */
    public function setNodeClass($type, $class)
    {
        $this->nodeMapping[strtolower($type)] = $class;
        return $this;
    }
    /**
     * @param string $type
     */
    protected function getNodeClass($type): string
    {
        $type = strtolower($type);
        if (!isset($this->nodeMapping[$type])) {
            throw new RuntimeException(sprintf('The node type "%s" is not registered.', $type));
        }
        $class = $this->nodeMapping[$type];
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('The node class "%s" does not exist.', $class));
        }
        return $class;
    }
}
