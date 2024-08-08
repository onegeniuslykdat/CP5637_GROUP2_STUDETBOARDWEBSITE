<?php

namespace Staatic\Framework\PostProcessor;

use ArrayIterator;
use IteratorAggregate;
use Staatic\Framework\Build;
use Traversable;
final class PostProcessorCollection implements IteratorAggregate
{
    /**
     * @var mixed[]
     */
    private $postProcessors = [];
    public function __construct(array $postProcessors = [])
    {
        $this->addPostProcessors($postProcessors);
    }
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->postProcessors);
    }
    /**
     * @param mixed[] $postProcessors
     */
    public function addPostProcessors($postProcessors): void
    {
        foreach ($postProcessors as $postProcessor) {
            $this->addPostProcessor($postProcessor);
        }
    }
    /**
     * @param PostProcessorInterface $postProcessor
     */
    public function addPostProcessor($postProcessor): void
    {
        $this->postProcessors[] = $postProcessor;
    }
    /**
     * @param Build $build
     */
    public function apply($build): void
    {
        foreach ($this->postProcessors as $postProcessor) {
            $postProcessor->apply($build);
        }
    }
}
