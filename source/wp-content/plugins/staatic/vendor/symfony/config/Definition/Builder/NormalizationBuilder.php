<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use Closure;
class NormalizationBuilder
{
    protected $node;
    public $before = [];
    public $declaredTypes = [];
    public $remappings = [];
    public function __construct(NodeDefinition $node)
    {
        $this->node = $node;
    }
    /**
     * @param string $key
     * @param string|null $plural
     * @return static
     */
    public function remap($key, $plural = null)
    {
        $this->remappings[] = [$key, (null === $plural) ? $key . 's' : $plural];
        return $this;
    }
    /**
     * @param Closure|null $closure
     * @return ExprBuilder|static
     */
    public function before($closure = null)
    {
        if (null !== $closure) {
            $this->before[] = $closure;
            return $this;
        }
        return $this->before[] = new ExprBuilder($this->node);
    }
}
