<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Util\WordpressEnv;

final class WorkDirectorySetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_work_directory';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Work Directory', 'staatic');
    }

    public function description(): ?string
    {
        return __('Temporary files created during publications are stored in this directory.', 'staatic');
    }

    public function defaultValue()
    {
        return WordpressEnv::getUploadsPath() . '/staatic';
    }
}
