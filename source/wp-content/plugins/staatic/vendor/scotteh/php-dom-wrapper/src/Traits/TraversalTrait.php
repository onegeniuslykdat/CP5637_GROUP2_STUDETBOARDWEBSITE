<?php

declare (strict_types=1);
namespace Staatic\Vendor\DOMWrap\Traits;

use DOMXPath;
use DOMNode;
use InvalidArgumentException;
use DOMDocument;
use Staatic\Vendor\DOMWrap\Element;
use Staatic\Vendor\DOMWrap\NodeList;
use Staatic\Vendor\Symfony\Component\CssSelector\CssSelectorConverter;
trait TraversalTrait
{
    protected static $cssSelectorConverter;
    /**
     * @param iterable|null $nodes
     */
    public function newNodeList($nodes = null): NodeList
    {
        if (!is_iterable($nodes)) {
            if (!is_null($nodes)) {
                $nodes = [$nodes];
            } else {
                $nodes = [];
            }
        }
        return new NodeList($this->document(), $nodes);
    }
    /**
     * @param string $selector
     * @param string $prefix
     */
    public function find($selector, $prefix = 'descendant::'): NodeList
    {
        if (!self::$cssSelectorConverter) {
            self::$cssSelectorConverter = new CssSelectorConverter();
        }
        return $this->findXPath(self::$cssSelectorConverter->toXPath($selector, $prefix));
    }
    /**
     * @param string $xpath
     */
    public function findXPath($xpath): NodeList
    {
        $results = $this->newNodeList();
        if ($this->isRemoved()) {
            return $results;
        }
        $domxpath = new DOMXPath($this->document());
        foreach ($this->collection() as $node) {
            $results = $results->merge($node->newNodeList($domxpath->query($xpath, $node)));
        }
        return $results;
    }
    /**
     * @param bool $matchType
     */
    protected function getNodesMatchingInput($input, $matchType = \true): NodeList
    {
        if ($input instanceof NodeList || $input instanceof DOMNode) {
            $inputNodes = $this->inputAsNodeList($input, \false);
            $fn = function ($node) use ($inputNodes) {
                return $inputNodes->exists($node);
            };
        } elseif (is_callable($input)) {
            $matchType = \true;
            $fn = $input;
        } elseif (is_string($input)) {
            $fn = function ($node) use ($input) {
                return $node->find($input, 'self::')->count() != 0;
            };
        } else {
            throw new InvalidArgumentException('Unexpected input value of type "' . gettype($input) . '"');
        }
        return $this->collection()->map(function ($node) use ($fn, $matchType) {
            if ($fn($node) !== $matchType) {
                return null;
            }
            return $node;
        });
    }
    public function is($input): bool
    {
        return $this->getNodesMatchingInput($input)->count() != 0;
    }
    public function not($input): NodeList
    {
        return $this->getNodesMatchingInput($input, \false);
    }
    public function filter($input): NodeList
    {
        return $this->getNodesMatchingInput($input);
    }
    public function has($input): NodeList
    {
        if ($input instanceof NodeList || $input instanceof DOMNode) {
            $inputNodes = $this->inputAsNodeList($input, \false);
            $fn = function ($node) use ($inputNodes) {
                $descendantNodes = $node->find('*', 'descendant::');
                return $inputNodes->reduce(function ($carry, $inputNode) use ($descendantNodes) {
                    if ($descendantNodes->exists($inputNode)) {
                        return \true;
                    }
                    return $carry;
                }, \false);
            };
        } elseif (is_string($input)) {
            $fn = function ($node) use ($input) {
                return $node->find($input, 'descendant::')->count() != 0;
            };
        } elseif (is_callable($input)) {
            $fn = $input;
        } else {
            throw new InvalidArgumentException('Unexpected input value of type "' . gettype($input) . '"');
        }
        return $this->getNodesMatchingInput($fn);
    }
    public function preceding($selector = null): ?DOMNode
    {
        return $this->precedingUntil(null, $selector)->first();
    }
    public function precedingAll($selector = null): NodeList
    {
        return $this->precedingUntil(null, $selector);
    }
    public function precedingUntil($input = null, $selector = null): NodeList
    {
        return $this->_walkPathUntil('previousSibling', $input, $selector);
    }
    public function following($selector = null): ?DOMNode
    {
        return $this->followingUntil(null, $selector)->first();
    }
    public function followingAll($selector = null): NodeList
    {
        return $this->followingUntil(null, $selector);
    }
    public function followingUntil($input = null, $selector = null): NodeList
    {
        return $this->_walkPathUntil('nextSibling', $input, $selector);
    }
    public function siblings($selector = null): NodeList
    {
        $results = $this->collection()->reduce(function ($carry, $node) use ($selector) {
            return $carry->merge($node->precedingAll($selector)->merge($node->followingAll($selector)));
        }, $this->newNodeList());
        return $results;
    }
    public function children(): NodeList
    {
        $results = $this->collection()->reduce(function ($carry, $node) {
            return $carry->merge($node->findXPath('child::*'));
        }, $this->newNodeList());
        return $results;
    }
    public function parent($selector = null)
    {
        $results = $this->_walkPathUntil('parentNode', null, $selector, self::$MATCH_TYPE_FIRST);
        return $this->result($results);
    }
    /**
     * @param int $index
     */
    public function eq($index): ?DOMNode
    {
        if ($index < 0) {
            $index = $this->collection()->count() + $index;
        }
        return $this->collection()->offsetGet($index);
    }
    /**
     * @param string|null $selector
     */
    public function parents($selector = null): NodeList
    {
        return $this->parentsUntil(null, $selector);
    }
    public function parentsUntil($input = null, $selector = null): NodeList
    {
        return $this->_walkPathUntil('parentNode', $input, $selector);
    }
    public function intersect(): DOMNode
    {
        if ($this->collection()->count() < 2) {
            return $this->collection()->first();
        }
        $nodeParents = [];
        $this->collection()->each(function ($node) use (&$nodeParents) {
            $nodeParents[] = $node->parents()->unshift($node)->toArray();
        });
        $diff = call_user_func_array('array_uintersect', array_merge($nodeParents, [function ($a, $b) {
            return strcmp(spl_object_hash($a), spl_object_hash($b));
        }]));
        return array_shift($diff);
    }
    public function closest($input)
    {
        $results = $this->_walkPathUntil('parentNode', $input, null, self::$MATCH_TYPE_LAST);
        return $this->result($results);
    }
    public function contents(): NodeList
    {
        $results = $this->collection()->reduce(function ($carry, $node) {
            if ($node->isRemoved()) {
                return $carry;
            }
            return $carry->merge($node->newNodeList($node->childNodes));
        }, $this->newNodeList());
        return $results;
    }
    public function add($input): NodeList
    {
        $nodes = $this->inputAsNodeList($input);
        $results = $this->collection()->merge($nodes);
        return $results;
    }
    private static $MATCH_TYPE_FIRST = 1;
    private static $MATCH_TYPE_LAST = 2;
    /**
     * @param DOMNode $baseNode
     * @param string $property
     * @param int|null $matchType
     */
    protected function _buildNodeListUntil($baseNode, $property, $input = null, $selector = null, $matchType = null): NodeList
    {
        $resultNodes = $this->newNodeList();
        $node = $baseNode->{$property};
        while ($node instanceof DOMNode && ($matchType === self::$MATCH_TYPE_FIRST || !$node instanceof DOMDocument)) {
            if ($matchType != self::$MATCH_TYPE_LAST && (is_null($selector) || $node->is($selector))) {
                $resultNodes[] = $node;
            }
            if ($matchType == self::$MATCH_TYPE_FIRST || !is_null($input) && $node->is($input)) {
                if ($matchType == self::$MATCH_TYPE_LAST) {
                    $resultNodes[] = $node;
                }
                break;
            }
            $node = $node->{$property};
        }
        return $resultNodes;
    }
    /**
     * @param iterable $nodeLists
     */
    protected function _uniqueNodes($nodeLists): NodeList
    {
        $resultNodes = $this->newNodeList();
        foreach ($nodeLists as $nodeList) {
            foreach ($nodeList as $node) {
                if (!$resultNodes->exists($node)) {
                    $resultNodes[] = $node;
                }
            }
        }
        return $resultNodes->reverse();
    }
    /**
     * @param string $property
     * @param int|null $matchType
     */
    protected function _walkPathUntil($property, $input = null, $selector = null, $matchType = null): NodeList
    {
        $nodeLists = [];
        $this->collection()->each(function ($node) use ($property, $input, $selector, $matchType, &$nodeLists) {
            $nodeLists[] = $this->_buildNodeListUntil($node, $property, $input, $selector, $matchType);
        });
        return $this->_uniqueNodes($nodeLists);
    }
}
