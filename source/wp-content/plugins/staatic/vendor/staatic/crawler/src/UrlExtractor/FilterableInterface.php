<?php

namespace Staatic\Crawler\UrlExtractor;

interface FilterableInterface
{
    /**
     * @param callable|null $callback
     */
    public function setFilterCallback($callback): void;
}
