<?php

namespace Staatic\Vendor\Symfony\Component\VarExporter;

interface LazyObjectInterface
{
    /**
     * @param bool $partial
     */
    public function isLazyObjectInitialized($partial = \false): bool;
    /**
     * @return object
     */
    public function initializeLazyObject();
    public function resetLazyObject(): bool;
}
