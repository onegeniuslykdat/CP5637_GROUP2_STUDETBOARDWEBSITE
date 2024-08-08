<?php

declare (strict_types=1);
namespace Staatic\Vendor\DOMWrap;

use BadMethodCallException;
use DOMDocument;
use DOMNode;
use Staatic\Vendor\DOMWrap\Traits\CommonTrait;
use Staatic\Vendor\DOMWrap\Traits\TraversalTrait;
use Staatic\Vendor\DOMWrap\Traits\ManipulationTrait;
use Staatic\Vendor\DOMWrap\Collections\NodeCollection;
class NodeList extends NodeCollection
{
    use CommonTrait;
    use TraversalTrait;
    use ManipulationTrait {
        ManipulationTrait::__call as __manipulationCall;
    }
    protected $document;
    public function __construct(Document $document = null, iterable $nodes = null)
    {
        parent::__construct($nodes);
        $this->document = $document;
    }
    public function __call(string $name, array $arguments)
    {
        try {
            $result = $this->__manipulationCall($name, $arguments);
        } catch (BadMethodCallException $e) {
            if (!$this->first() || !method_exists($this->first(), $name)) {
                throw new BadMethodCallException("Call to undefined method " . get_class($this) . '::' . $name . "()");
            }
            $result = call_user_func_array([$this->first(), $name], $arguments);
        }
        return $result;
    }
    public function collection(): NodeList
    {
        return $this;
    }
    public function document(): ?DOMDocument
    {
        return $this->document;
    }
    /**
     * @param \Staatic\Vendor\DOMWrap\NodeList $nodeList
     */
    public function result($nodeList)
    {
        return $nodeList;
    }
    public function reverse(): NodeList
    {
        array_reverse($this->nodes);
        return $this;
    }
    public function first()
    {
        return (!empty($this->nodes)) ? $this->rewind() : null;
    }
    public function last()
    {
        return $this->end();
    }
    public function end()
    {
        return (!empty($this->nodes)) ? end($this->nodes) : null;
    }
    /**
     * @param int $key
     */
    public function get($key)
    {
        if (isset($this->nodes[$key])) {
            return $this->nodes[$key];
        }
        return null;
    }
    /**
     * @param int $key
     */
    public function set($key, $value): self
    {
        $this->nodes[$key] = $value;
        return $this;
    }
    /**
     * @param callable $function
     */
    public function each($function): self
    {
        foreach ($this->nodes as $index => $node) {
            $result = $function($node, $index);
            if ($result === \false) {
                break;
            }
        }
        return $this;
    }
    /**
     * @param callable $function
     */
    public function map($function): NodeList
    {
        $nodes = $this->newNodeList();
        foreach ($this->nodes as $node) {
            $result = $function($node);
            if (!is_null($result) && $result !== \false) {
                $nodes[] = $result;
            }
        }
        return $nodes;
    }
    /**
     * @param callable $function
     */
    public function reduce($function, $initial = null)
    {
        return array_reduce($this->nodes, $function, $initial);
    }
    public function toArray()
    {
        return $this->nodes;
    }
    /**
     * @param iterable|null $nodes
     */
    public function fromArray($nodes = null)
    {
        $this->nodes = [];
        if (is_iterable($nodes)) {
            foreach ($nodes as $node) {
                $this->nodes[] = $node;
            }
        }
    }
    public function merge($elements = []): NodeList
    {
        if (!is_array($elements)) {
            $elements = $elements->toArray();
        }
        return $this->newNodeList(array_merge($this->toArray(), $elements));
    }
    /**
     * @param int $start
     * @param int|null $end
     */
    public function slice($start, $end = null): NodeList
    {
        $nodeList = array_slice($this->toArray(), $start, $end);
        return $this->newNodeList($nodeList);
    }
    /**
     * @param DOMNode $node
     */
    public function push($node): self
    {
        $this->nodes[] = $node;
        return $this;
    }
    public function pop(): DOMNode
    {
        return array_pop($this->nodes);
    }
    /**
     * @param DOMNode $node
     */
    public function unshift($node): self
    {
        array_unshift($this->nodes, $node);
        return $this;
    }
    public function shift(): DOMNode
    {
        return array_shift($this->nodes);
    }
    /**
     * @param DOMNode $node
     */
    public function exists($node): bool
    {
        return in_array($node, $this->nodes, \true);
    }
    /**
     * @param DOMNode $node
     */
    public function delete($node): self
    {
        $index = array_search($node, $this->nodes, \true);
        if ($index !== \false) {
            unset($this->nodes[$index]);
        }
        return $this;
    }
    public function isRemoved(): bool
    {
        return \false;
    }
}
