<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class CloudFrontDistributionIdSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_aws_cloudfront_distribution_id';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Distribution ID', 'staatic');
    }

    public function description(): string
    {
        return sprintf(
            __('The CloudFront Distribution ID allows cache to be invalidated automatically.<br>In order to disable CloudFront integration, leave this value empty.', 'staatic'),
            'https://docs.aws.amazon.com/AmazonS3/latest/dev/website-hosting-cloudfront-walkthrough.html'
        );
    }
}
