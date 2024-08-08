<?php

namespace Staatic\Vendor\voku\helper;

use IteratorAggregate;
interface SimpleXmlDomNodeInterface extends IteratorAggregate
{
    public function __get($name);
    public function __invoke($selector, $idx = null);
    public function __toString();
    public function count();
    /**
     * @param string $selector
     */
    public function find($selector, $idx = null);
    /**
     * @param string $selector
     */
    public function findMulti($selector): self;
    /**
     * @param string $selector
     */
    public function findMultiOrFalse($selector);
    /**
     * @param string $selector
     */
    public function findOne($selector);
    /**
     * @param string $selector
     */
    public function findOneOrFalse($selector);
    public function innerHtml(): array;
    public function innertext();
    public function outertext();
    public function text(): array;
}
