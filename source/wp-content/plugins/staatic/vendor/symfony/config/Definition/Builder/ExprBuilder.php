<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use Closure;
use InvalidArgumentException;
use RuntimeException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\UnsetKeyException;
class ExprBuilder
{
    public const TYPE_ANY = 'any';
    public const TYPE_STRING = 'string';
    public const TYPE_NULL = 'null';
    public const TYPE_ARRAY = 'array';
    protected $node;
    public $allowedTypes;
    public $ifPart;
    public $thenPart;
    public function __construct(NodeDefinition $node)
    {
        $this->node = $node;
    }
    /**
     * @param Closure|null $then
     * @return static
     */
    public function always($then = null)
    {
        $this->ifPart = static function () {
            return \true;
        };
        $this->allowedTypes = self::TYPE_ANY;
        if (null !== $then) {
            $this->thenPart = $then;
        }
        return $this;
    }
    /**
     * @param Closure|null $closure
     * @return static
     */
    public function ifTrue($closure = null)
    {
        $this->ifPart = $closure ?? static function ($v) {
            return \true === $v;
        };
        $this->allowedTypes = self::TYPE_ANY;
        return $this;
    }
    /**
     * @return static
     */
    public function ifString()
    {
        $this->ifPart = Closure::fromCallable('is_string');
        $this->allowedTypes = self::TYPE_STRING;
        return $this;
    }
    /**
     * @return static
     */
    public function ifNull()
    {
        $this->ifPart = Closure::fromCallable('is_null');
        $this->allowedTypes = self::TYPE_NULL;
        return $this;
    }
    /**
     * @return static
     */
    public function ifEmpty()
    {
        $this->ifPart = static function ($v) {
            return empty($v);
        };
        $this->allowedTypes = self::TYPE_ANY;
        return $this;
    }
    /**
     * @return static
     */
    public function ifArray()
    {
        $this->ifPart = Closure::fromCallable('is_array');
        $this->allowedTypes = self::TYPE_ARRAY;
        return $this;
    }
    /**
     * @param mixed[] $array
     * @return static
     */
    public function ifInArray($array)
    {
        $this->ifPart = static function ($v) use ($array) {
            return \in_array($v, $array, \true);
        };
        $this->allowedTypes = self::TYPE_ANY;
        return $this;
    }
    /**
     * @param mixed[] $array
     * @return static
     */
    public function ifNotInArray($array)
    {
        $this->ifPart = static function ($v) use ($array) {
            return !\in_array($v, $array, \true);
        };
        $this->allowedTypes = self::TYPE_ANY;
        return $this;
    }
    /**
     * @return static
     */
    public function castToArray()
    {
        $this->ifPart = static function ($v) {
            return !\is_array($v);
        };
        $this->allowedTypes = self::TYPE_ANY;
        $this->thenPart = static function ($v) {
            return [$v];
        };
        return $this;
    }
    /**
     * @param Closure $closure
     * @return static
     */
    public function then($closure)
    {
        $this->thenPart = $closure;
        return $this;
    }
    /**
     * @return static
     */
    public function thenEmptyArray()
    {
        $this->thenPart = static function () {
            return [];
        };
        return $this;
    }
    /**
     * @param string $message
     * @return static
     */
    public function thenInvalid($message)
    {
        $this->thenPart = static function ($v) use ($message) {
            throw new InvalidArgumentException(sprintf($message, json_encode($v)));
        };
        return $this;
    }
    /**
     * @return static
     */
    public function thenUnset()
    {
        $this->thenPart = static function () {
            throw new UnsetKeyException('Unsetting key.');
        };
        return $this;
    }
    /**
     * @return NodeDefinition|ArrayNodeDefinition|VariableNodeDefinition
     */
    public function end()
    {
        if (null === $this->ifPart) {
            throw new RuntimeException('You must specify an if part.');
        }
        if (null === $this->thenPart) {
            throw new RuntimeException('You must specify a then part.');
        }
        return $this->node;
    }
    /**
     * @param mixed[] $expressions
     */
    public static function buildExpressions($expressions): array
    {
        foreach ($expressions as $k => $expr) {
            if ($expr instanceof self) {
                $if = $expr->ifPart;
                $then = $expr->thenPart;
                $expressions[$k] = static function ($v) use ($if, $then) {
                    return $if($v) ? $then($v) : $v;
                };
            }
        }
        return $expressions;
    }
}
