<?php

namespace Staatic\Crawler\UrlExtractor;

interface TransformableInterface
{
    /**
     * @param callable|null $callback
     */
    public function setTransformCallback($callback): void;
}
