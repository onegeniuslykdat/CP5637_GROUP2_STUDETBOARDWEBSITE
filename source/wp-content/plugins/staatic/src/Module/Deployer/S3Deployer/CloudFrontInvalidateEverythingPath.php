<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class CloudFrontInvalidateEverythingPath extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_aws_cloudfront_invalidate_everything_path';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Invalidate Everything Path', 'staatic');
    }

    public function description(): string
    {
        return __('The path to invalidate when Maximum Invalidation Paths has been exceeded (usually <code>/*</code>).', 'staatic');
    }

    public function defaultValue()
    {
        return '/*';
    }
}
