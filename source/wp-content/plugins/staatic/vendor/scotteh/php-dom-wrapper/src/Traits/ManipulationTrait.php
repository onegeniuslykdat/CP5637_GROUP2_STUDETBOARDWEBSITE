<?php

declare (strict_types=1);
namespace Staatic\Vendor\DOMWrap\Traits;

use BadMethodCallException;
use DOMNode;
use InvalidArgumentException;
use DOMElement;
use SplStack;
use Staatic\Vendor\DOMWrap\Text;
use Staatic\Vendor\DOMWrap\Element;
use Staatic\Vendor\DOMWrap\NodeList;
trait ManipulationTrait
{
    public function __call(string $name, array $arguments)
    {
        if (!method_exists($this, '_' . $name)) {
            throw new BadMethodCallException("Call to undefined method " . get_class($this) . '::' . $name . "()");
        }
        return call_user_func_array([$this, '_' . $name], $arguments);
    }
    public function __toString(): string
    {
        return $this->getOuterHtml(\true);
    }
    protected function inputPrepareAsTraversable($input): iterable
    {
        if ($input instanceof DOMNode) {
            if (!method_exists($input, 'inputPrepareAsTraversable')) {
                $input = $this->document()->importNode($input, \true);
            }
            $nodes = [$input];
        } else if (is_string($input)) {
            $nodes = $this->nodesFromHtml($input);
        } else if (is_iterable($input)) {
            $nodes = $input;
        } else {
            throw new InvalidArgumentException();
        }
        return $nodes;
    }
    protected function inputAsNodeList($input, $cloneForManipulate = \true): NodeList
    {
        $nodes = $this->inputPrepareAsTraversable($input);
        $newNodes = $this->newNodeList();
        foreach ($nodes as $node) {
            if ($node->document() !== $this->document()) {
                $node = $this->document()->importNode($node, \true);
            }
            if ($cloneForManipulate && $node->parentNode !== null) {
                $node = $node->cloneNode(\true);
            }
            $newNodes[] = $node;
        }
        return $newNodes;
    }
    protected function inputAsFirstNode($input): ?DOMNode
    {
        $nodes = $this->inputAsNodeList($input);
        return $nodes->findXPath('self::*')->first();
    }
    protected function nodesFromHtml($html): NodeList
    {
        $class = get_class($this->document());
        $doc = new $class();
        $doc->setEncoding($this->document()->getEncoding());
        $nodes = $doc->html($html)->find('body')->contents();
        return $nodes;
    }
    /**
     * @param callable $callback
     */
    protected function manipulateNodesWithInput($input, $callback): self
    {
        $this->collection()->each(function ($node, $index) use ($input, $callback) {
            $html = $input;
            if (is_callable($input)) {
                $html = $input($node, $index);
            }
            $newNodes = $this->inputAsNodeList($html);
            $callback($node, $newNodes);
        });
        return $this;
    }
    /**
     * @param string|null $selector
     */
    public function detach($selector = null): NodeList
    {
        if (!is_null($selector)) {
            $nodes = $this->find($selector, 'self::');
        } else {
            $nodes = $this->collection();
        }
        $nodeList = $this->newNodeList();
        $nodes->each(function ($node) use ($nodeList) {
            if ($node->parent() instanceof DOMNode) {
                $nodeList[] = $node->parent()->removeChild($node);
            }
        });
        $nodes->fromArray([]);
        return $nodeList;
    }
    /**
     * @param string|null $selector
     */
    public function destroy($selector = null): self
    {
        $this->detach($selector);
        return $this;
    }
    public function substituteWith($input): self
    {
        $this->manipulateNodesWithInput($input, function ($node, $newNodes) {
            foreach ($newNodes as $newNode) {
                $node->parent()->replaceChild($newNode, $node);
            }
        });
        return $this;
    }
    public function text($input = null)
    {
        if (is_null($input)) {
            return $this->getText();
        } else {
            return $this->setText($input);
        }
    }
    public function getText(): string
    {
        return (string) $this->collection()->reduce(function ($carry, $node) {
            return $carry . $node->textContent;
        }, '');
    }
    public function setText($input): self
    {
        if (is_string($input)) {
            $input = new Text($input);
        }
        $this->manipulateNodesWithInput($input, function ($node, $newNodes) {
            $node->contents()->destroy();
            $node->appendWith(new Text($newNodes->getText()));
        });
        return $this;
    }
    public function precede($input): self
    {
        $this->manipulateNodesWithInput($input, function ($node, $newNodes) {
            foreach ($newNodes as $newNode) {
                $node->parent()->insertBefore($newNode, $node);
            }
        });
        return $this;
    }
    public function follow($input): self
    {
        $this->manipulateNodesWithInput($input, function ($node, $newNodes) {
            foreach ($newNodes as $newNode) {
                if (is_null($node->following())) {
                    $node->parent()->appendChild($newNode);
                } else {
                    $node->parent()->insertBefore($newNode, $node->following());
                }
            }
        });
        return $this;
    }
    public function prependWith($input): self
    {
        $this->manipulateNodesWithInput($input, function ($node, $newNodes) {
            foreach ($newNodes as $newNode) {
                $node->insertBefore($newNode, $node->contents()->first());
            }
        });
        return $this;
    }
    public function appendWith($input): self
    {
        $this->manipulateNodesWithInput($input, function ($node, $newNodes) {
            foreach ($newNodes as $newNode) {
                $node->appendChild($newNode);
            }
        });
        return $this;
    }
    public function prependTo($selector): self
    {
        if ($selector instanceof DOMNode || $selector instanceof NodeList) {
            $nodes = $this->inputAsNodeList($selector);
        } else {
            $nodes = $this->document()->find($selector);
        }
        $nodes->prependWith($this);
        return $this;
    }
    public function appendTo($selector): self
    {
        if ($selector instanceof DOMNode || $selector instanceof NodeList) {
            $nodes = $this->inputAsNodeList($selector);
        } else {
            $nodes = $this->document()->find($selector);
        }
        $nodes->appendWith($this);
        return $this;
    }
    public function _empty(): self
    {
        $this->collection()->each(function ($node) {
            $node->contents()->destroy();
        });
        return $this;
    }
    public function _clone()
    {
        $clonedNodes = $this->newNodeList();
        $this->collection()->each(function ($node) use ($clonedNodes) {
            $clonedNodes[] = $node->cloneNode(\true);
        });
        return $this->result($clonedNodes);
    }
    /**
     * @param string $name
     */
    public function removeAttr($name): self
    {
        $this->collection()->each(function ($node) use ($name) {
            if ($node instanceof DOMElement) {
                $node->removeAttribute($name);
            }
        });
        return $this;
    }
    /**
     * @param string $name
     */
    public function hasAttr($name): bool
    {
        return (bool) $this->collection()->reduce(function ($carry, $node) use ($name) {
            if ($node->hasAttribute($name)) {
                return \true;
            }
            return $carry;
        }, \false);
    }
    /**
     * @param string $name
     */
    public function getAttr($name): string
    {
        $node = $this->collection()->first();
        if (!$node instanceof DOMElement) {
            return '';
        }
        return $node->getAttribute($name);
    }
    /**
     * @param string $name
     */
    public function setAttr($name, $value): self
    {
        $this->collection()->each(function ($node) use ($name, $value) {
            if ($node instanceof DOMElement) {
                $node->setAttribute($name, (string) $value);
            }
        });
        return $this;
    }
    /**
     * @param string $name
     */
    public function attr($name, $value = null)
    {
        if (is_null($value)) {
            return $this->getAttr($name);
        } else {
            return $this->setAttr($name, $value);
        }
    }
    /**
     * @param string $name
     * @param bool $addValue
     */
    protected function _pushAttrValue($name, $value, $addValue = \false): void
    {
        $this->collection()->each(function ($node, $index) use ($name, $value, $addValue) {
            if ($node instanceof DOMElement) {
                $attr = $node->getAttribute($name);
                if (is_callable($value)) {
                    $value = $value($node, $index, $attr);
                }
                $values = array_filter(explode(' ', $attr), function ($_value) use ($value) {
                    if (strcasecmp($_value, $value) == 0 || empty($_value)) {
                        return \false;
                    }
                    return \true;
                });
                if ($addValue) {
                    $values[] = $value;
                }
                if (!empty($values) || $node->hasAttribute($name)) {
                    $node->setAttribute($name, implode(' ', $values));
                }
            }
        });
    }
    public function addClass($class): self
    {
        $this->_pushAttrValue('class', $class, \true);
        return $this;
    }
    public function removeClass($class): self
    {
        $this->_pushAttrValue('class', $class);
        return $this;
    }
    /**
     * @param string $class
     */
    public function hasClass($class): bool
    {
        return (bool) $this->collection()->reduce(function ($carry, $node) use ($class) {
            $attr = $node->getAttr('class');
            return array_reduce(explode(' ', (string) $attr), function ($carry, $item) use ($class) {
                if (strcasecmp($item, $class) == 0) {
                    return \true;
                }
                return $carry;
            }, \false);
        }, \false);
    }
    /**
     * @param Element $node
     */
    protected function _getFirstChildWrapStack($node): SplStack
    {
        $stack = new SplStack();
        do {
            $stack->push($node);
            $node = $node->children()->first();
        } while ($node instanceof Element);
        return $stack;
    }
    /**
     * @param Element $node
     */
    protected function _prepareWrapStack($node): SplStack
    {
        $stackNodes = $this->_getFirstChildWrapStack($node);
        foreach ($stackNodes as $stackNode) {
            $stackNode->siblings()->destroy();
        }
        return $stackNodes;
    }
    /**
     * @param callable $callback
     */
    protected function wrapWithInputByCallback($input, $callback): void
    {
        $this->collection()->each(function ($node, $index) use ($input, $callback) {
            $html = $input;
            if (is_callable($input)) {
                $html = $input($node, $index);
            }
            $inputNode = $this->inputAsFirstNode($html);
            if ($inputNode instanceof Element) {
                $stackNodes = $this->_prepareWrapStack($inputNode);
                $callback($node, $stackNodes);
            }
        });
    }
    public function wrapInner($input): self
    {
        $this->wrapWithInputByCallback($input, function ($node, $stackNodes) {
            foreach ($node->contents() as $child) {
                $oldChild = $child->detach()->first();
                $stackNodes->top()->appendWith($oldChild);
            }
            $node->appendWith($stackNodes->bottom());
        });
        return $this;
    }
    public function wrap($input): self
    {
        $this->wrapWithInputByCallback($input, function ($node, $stackNodes) {
            $node->follow($stackNodes->bottom());
            $oldNode = $node->detach()->first();
            $stackNodes->top()->appendWith($oldNode);
        });
        return $this;
    }
    public function wrapAll($input): self
    {
        if (!$this->collection()->count()) {
            return $this;
        }
        if (is_callable($input)) {
            $input = $input($this->collection()->first());
        }
        $inputNode = $this->inputAsFirstNode($input);
        if (!$inputNode instanceof Element) {
            return $this;
        }
        $stackNodes = $this->_prepareWrapStack($inputNode);
        $this->collection()->first()->precede($stackNodes->bottom());
        $this->collection()->each(function ($node) use ($stackNodes) {
            $stackNodes->top()->appendWith($node->detach());
        });
        return $this;
    }
    public function unwrap(): self
    {
        $this->collection()->each(function ($node) {
            $parent = $node->parent();
            $parent->contents()->each(function ($childNode) use ($parent) {
                $oldChildNode = $childNode->detach()->first();
                $parent->precede($oldChildNode);
            });
            $parent->destroy();
        });
        return $this;
    }
    /**
     * @param bool $isIncludeAll
     */
    public function getOuterHtml($isIncludeAll = \false): string
    {
        $nodes = $this->collection();
        if (!$isIncludeAll) {
            $nodes = $this->newNodeList([$nodes->first()]);
        }
        return $nodes->reduce(function ($carry, $node) {
            return $carry . $this->document()->saveHTML($node);
        }, '');
    }
    /**
     * @param bool $isIncludeAll
     */
    public function getHtml($isIncludeAll = \false): string
    {
        $nodes = $this->collection();
        if (!$isIncludeAll) {
            $nodes = $this->newNodeList([$nodes->first()]);
        }
        return $nodes->contents()->reduce(function ($carry, $node) {
            return $carry . $this->document()->saveHTML($node);
        }, '');
    }
    public function setHtml($input): self
    {
        $this->manipulateNodesWithInput($input, function ($node, $newNodes) {
            $node->contents()->destroy();
            $node->appendWith($newNodes);
        });
        return $this;
    }
    public function html($input = null)
    {
        if (is_null($input)) {
            return $this->getHtml();
        } else {
            return $this->setHtml($input);
        }
    }
    public function create($input): NodeList
    {
        return $this->inputAsNodeList($input);
    }
}
