<?php

declare(strict_types=1);

namespace Staatic\WordPress\Logging;

interface Contextable
{
    /**
     * @param mixed[] $context
     */
    public function changeContext($context): void;
}
