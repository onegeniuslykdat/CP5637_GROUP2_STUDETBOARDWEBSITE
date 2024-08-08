<?php

namespace Staatic\Framework\ConfigGenerator;

use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Framework\Result;
interface ConfigGeneratorInterface
{
    /**
     * @param Result $result
     */
    public function processResult($result): void;
    public function getFiles(): iterable;
}
