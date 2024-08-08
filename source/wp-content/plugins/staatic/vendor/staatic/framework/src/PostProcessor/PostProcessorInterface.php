<?php

namespace Staatic\Framework\PostProcessor;

use Staatic\Framework\Build;
interface PostProcessorInterface
{
    public function createsOrRemovesResults(): bool;
    /**
     * @param Build $build
     */
    public function apply($build): void;
}
