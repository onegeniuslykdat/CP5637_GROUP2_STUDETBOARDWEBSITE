<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

class ServiceReferenceGraphEdge
{
    /**
     * @var ServiceReferenceGraphNode
     */
    private $sourceNode;
    /**
     * @var ServiceReferenceGraphNode
     */
    private $destNode;
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var bool
     */
    private $lazy;
    /**
     * @var bool
     */
    private $weak;
    /**
     * @var bool
     */
    private $byConstructor;
    /**
     * @param mixed $value
     */
    public function __construct(ServiceReferenceGraphNode $sourceNode, ServiceReferenceGraphNode $destNode, $value = null, bool $lazy = \false, bool $weak = \false, bool $byConstructor = \false)
    {
        $this->sourceNode = $sourceNode;
        $this->destNode = $destNode;
        $this->value = $value;
        $this->lazy = $lazy;
        $this->weak = $weak;
        $this->byConstructor = $byConstructor;
    }
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    public function getSourceNode(): ServiceReferenceGraphNode
    {
        return $this->sourceNode;
    }
    public function getDestNode(): ServiceReferenceGraphNode
    {
        return $this->destNode;
    }
    public function isLazy(): bool
    {
        return $this->lazy;
    }
    public function isWeak(): bool
    {
        return $this->weak;
    }
    public function isReferencedByConstructor(): bool
    {
        return $this->byConstructor;
    }
}
