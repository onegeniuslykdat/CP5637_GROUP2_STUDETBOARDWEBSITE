<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting;

interface ComposedSettingInterface
{
    /**
     * @return SettingInterface[]
     */
    public function settings(): array;
}
