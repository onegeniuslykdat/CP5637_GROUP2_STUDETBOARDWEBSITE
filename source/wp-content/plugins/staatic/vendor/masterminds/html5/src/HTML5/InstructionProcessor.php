<?php

namespace Staatic\Vendor\Masterminds\HTML5;

use DOMElement;
interface InstructionProcessor
{
    /**
     * @param DOMElement $element
     */
    public function process($element, $name, $data);
}
