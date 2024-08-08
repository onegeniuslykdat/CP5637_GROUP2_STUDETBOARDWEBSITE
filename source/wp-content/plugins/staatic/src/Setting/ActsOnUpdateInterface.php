<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting;

interface ActsOnUpdateInterface
{
    public function onUpdate($value, $valueBefore): void;
}
