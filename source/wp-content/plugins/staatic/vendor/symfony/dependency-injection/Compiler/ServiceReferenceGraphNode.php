<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Alias;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
class ServiceReferenceGraphNode
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var mixed[]
     */
    private $inEdges = [];
    /**
     * @var mixed[]
     */
    private $outEdges = [];
    /**
     * @var mixed
     */
    private $value;
    /**
     * @param mixed $value
     */
    public function __construct(string $id, $value)
    {
        $this->id = $id;
        $this->value = $value;
    }
    /**
     * @param ServiceReferenceGraphEdge $edge
     */
    public function addInEdge($edge)
    {
        $this->inEdges[] = $edge;
    }
    /**
     * @param ServiceReferenceGraphEdge $edge
     */
    public function addOutEdge($edge)
    {
        $this->outEdges[] = $edge;
    }
    public function isAlias(): bool
    {
        return $this->value instanceof Alias;
    }
    public function isDefinition(): bool
    {
        return $this->value instanceof Definition;
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getInEdges(): array
    {
        return $this->inEdges;
    }
    public function getOutEdges(): array
    {
        return $this->outEdges;
    }
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    public function clear()
    {
        $this->inEdges = $this->outEdges = [];
    }
}
