<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class ServiceReferenceGraph
{
    /**
     * @var mixed[]
     */
    private $nodes = [];
    /**
     * @param string $id
     */
    public function hasNode($id): bool
    {
        return isset($this->nodes[$id]);
    }
    /**
     * @param string $id
     */
    public function getNode($id): ServiceReferenceGraphNode
    {
        if (!isset($this->nodes[$id])) {
            throw new InvalidArgumentException(sprintf('There is no node with id "%s".', $id));
        }
        return $this->nodes[$id];
    }
    public function getNodes(): array
    {
        return $this->nodes;
    }
    public function clear()
    {
        foreach ($this->nodes as $node) {
            $node->clear();
        }
        $this->nodes = [];
    }
    /**
     * @param string|null $sourceId
     * @param mixed $sourceValue
     * @param string|null $destId
     * @param mixed $destValue
     * @param Reference|null $reference
     * @param bool $lazy
     * @param bool $weak
     * @param bool $byConstructor
     */
    public function connect($sourceId, $sourceValue, $destId, $destValue = null, $reference = null, $lazy = \false, $weak = \false, $byConstructor = \false)
    {
        if (null === $sourceId || null === $destId) {
            return;
        }
        $sourceNode = $this->createNode($sourceId, $sourceValue);
        $destNode = $this->createNode($destId, $destValue);
        $edge = new ServiceReferenceGraphEdge($sourceNode, $destNode, $reference, $lazy, $weak, $byConstructor);
        $sourceNode->addOutEdge($edge);
        $destNode->addInEdge($edge);
    }
    /**
     * @param mixed $value
     */
    private function createNode(string $id, $value): ServiceReferenceGraphNode
    {
        if (isset($this->nodes[$id]) && $this->nodes[$id]->getValue() === $value) {
            return $this->nodes[$id];
        }
        return $this->nodes[$id] = new ServiceReferenceGraphNode($id, $value);
    }
}
