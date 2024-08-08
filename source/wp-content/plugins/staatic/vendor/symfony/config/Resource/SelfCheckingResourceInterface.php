<?php

namespace Staatic\Vendor\Symfony\Component\Config\Resource;

interface SelfCheckingResourceInterface extends ResourceInterface
{
    /**
     * @param int $timestamp
     */
    public function isFresh($timestamp): bool;
}
