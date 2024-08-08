<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting;

use Staatic\WordPress\Service\PartialRenderer;

interface RendersPartialsInterface
{
    /**
     * @param PartialRenderer $renderer
     */
    public function setPartialRenderer($renderer): void;
}
