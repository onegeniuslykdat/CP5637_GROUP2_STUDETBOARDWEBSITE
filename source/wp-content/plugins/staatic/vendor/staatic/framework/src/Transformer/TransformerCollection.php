<?php

namespace Staatic\Framework\Transformer;

use ArrayIterator;
use IteratorAggregate;
use RuntimeException;
use Staatic\Framework\Resource;
use Staatic\Framework\Result;
use Traversable;
final class TransformerCollection implements IteratorAggregate
{
    /**
     * @var mixed[]
     */
    private $transformers = [];
    public function __construct(array $transformers = [])
    {
        $this->addTransformers($transformers);
    }
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->transformers);
    }
    /**
     * @param mixed[] $transformers
     */
    public function addTransformers($transformers): void
    {
        foreach ($transformers as $transformer) {
            $this->addTransformer($transformer);
        }
    }
    /**
     * @param TransformerInterface $transformer
     */
    public function addTransformer($transformer): void
    {
        $this->transformers[] = $transformer;
    }
    /**
     * @param Result $result
     * @param Resource $resource
     */
    public function apply($result, $resource): void
    {
        foreach ($this->transformers as $transformer) {
            if (!$transformer->supports($result, $resource)) {
                continue;
            }
            $transformer->transform($result, $resource);
            if ($resource->content()->tell() !== 0) {
                throw new RuntimeException(sprintf('Resource content stream was not left in a valid state since "%s"', get_class($transformer)));
            }
        }
    }
}
