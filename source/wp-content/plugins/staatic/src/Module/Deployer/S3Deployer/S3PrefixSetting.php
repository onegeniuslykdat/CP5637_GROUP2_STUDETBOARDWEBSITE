<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class S3PrefixSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_aws_s3_prefix';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Prefix', 'staatic');
    }

    public function description(): string
    {
        return sprintf(
            /* translators: %s: Example prefix. */
            __('Optionally add a prefix in order to store the static site\'s data in a subdirectory.<br>Example: <code>%s</code>.', 'staatic'),
            'some/subdirectory/'
        );
    }
}
