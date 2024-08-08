<?php

namespace Staatic\Framework\Transformer;

use Staatic\Framework\Resource;
use Staatic\Framework\Result;
interface TransformerInterface
{
    /**
     * @param Result $result
     */
    public function supports($result): bool;
    /**
     * @param Result $result
     * @param Resource $resource
     */
    public function transform($result, $resource): void;
}
