<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

interface ModuleInterface
{
    public function hooks(): void;
}
