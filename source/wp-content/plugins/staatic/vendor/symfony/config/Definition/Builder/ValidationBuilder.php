<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use Closure;
class ValidationBuilder
{
    protected $node;
    public $rules = [];
    public function __construct(NodeDefinition $node)
    {
        $this->node = $node;
    }
    /**
     * @param Closure|null $closure
     * @return ExprBuilder|static
     */
    public function rule($closure = null)
    {
        if (null !== $closure) {
            $this->rules[] = $closure;
            return $this;
        }
        return $this->rules[] = new ExprBuilder($this->node);
    }
}
