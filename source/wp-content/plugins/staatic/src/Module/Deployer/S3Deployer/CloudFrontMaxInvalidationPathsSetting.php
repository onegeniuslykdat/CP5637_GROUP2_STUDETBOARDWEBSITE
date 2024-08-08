<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class CloudFrontMaxInvalidationPathsSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_aws_cloudfront_max_invalidation_paths';
    }

    public function type(): string
    {
        return self::TYPE_INTEGER;
    }

    public function label(): string
    {
        return __('Maximum Invalidation Paths', 'staatic');
    }

    public function description(): string
    {
        return __('The maximum number of invalidation paths before invalidating everything.', 'staatic');
    }

    public function defaultValue()
    {
        return 50;
    }
}
