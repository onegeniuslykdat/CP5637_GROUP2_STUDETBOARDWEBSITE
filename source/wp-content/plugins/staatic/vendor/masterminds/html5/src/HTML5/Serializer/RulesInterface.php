<?php

namespace Staatic\Vendor\Masterminds\HTML5\Serializer;

interface RulesInterface
{
    public function __construct($output, $options = array());
    /**
     * @param Traverser $traverser
     */
    public function setTraverser($traverser);
    public function document($dom);
    public function element($ele);
    public function text($ele);
    public function cdata($ele);
    public function comment($ele);
    public function processorInstruction($ele);
}
