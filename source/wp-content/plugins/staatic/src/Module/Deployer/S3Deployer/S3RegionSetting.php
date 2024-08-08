<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class S3RegionSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_aws_s3_region';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    protected function template(): string
    {
        return 'string';
    }

    public function label(): string
    {
        return __('Region', 'staatic');
    }

    public function description(): string
    {
        return __('The name of the region the S3 bucket resides in.', 'staatic');
    }

    public function defaultValue()
    {
        return 'us-east-1';
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        parent::render(array_merge([
            'options' => $this->options()
        ], $attributes));
    }

    private function options(): array
    {
        $regions = [
            'us-east-2' => 'US East (Ohio)',
            'us-east-1' => 'US East (N. Virginia)',
            'us-west-1' => 'US West (N. California)',
            'us-west-2' => 'US West (Oregon)',
            'af-south-1' => 'Africa (Cape Town)',
            'ap-east-1' => 'Asia Pacific (Hong Kong)',
            'ap-south-2' => 'Asia Pacific (Hyderabad)',
            'ap-southeast-3' => 'Asia Pacific (Jakarta)',
            'ap-southeast-4' => 'Asia Pacific (Melbourne)',
            'ap-south-1' => 'Asia Pacific (Mumbai)',
            'ap-northeast-3' => 'Asia Pacific (Osaka)',
            'ap-northeast-2' => 'Asia Pacific (Seoul)',
            'ap-southeast-1' => 'Asia Pacific (Singapore)',
            'ap-southeast-2' => 'Asia Pacific (Sydney)',
            'ap-northeast-1' => 'Asia Pacific (Tokyo)',
            'ca-central-1' => 'Canada (Central)',
            'ca-west-1' => 'Canada (Calgary)',
            'eu-central-1' => 'Europe (Frankfurt)',
            'eu-west-1' => 'Europe (Ireland)',
            'eu-west-2' => 'Europe (London)',
            'eu-south-1' => 'Europe (Milan)',
            'eu-west-3' => 'Europe (Paris)',
            'eu-south-2' => 'Europe (Spain)',
            'eu-north-1' => 'Europe (Stockholm)',
            'eu-central-2' => 'Europe (Zurich)',
            'il-central-1' => 'Israel (Tel Aviv)',
            'me-south-1' => 'Middle East (Bahrain)',
            'me-central-1' => 'Middle East (UAE)',
            'sa-east-1' => 'South America (São Paulo)',
            'cn-north-1' => 'China (Beijing)',
            'cn-northwest-1' => 'China (Ningxia)'
        ];
        foreach ($regions as $region => $label) {
            $regions[$region] = sprintf('%s > %s', $label, $region);
        }

        return $regions;
    }
}
