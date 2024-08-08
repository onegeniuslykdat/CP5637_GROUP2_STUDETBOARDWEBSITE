<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\ComposedSettingInterface;

final class S3Setting extends AbstractSetting implements ComposedSettingInterface
{
    /**
     * @var ServiceLocator
     */
    private $settingLocator;

    public function __construct(ServiceLocator $settingLocator)
    {
        $this->settingLocator = $settingLocator;
    }

    public function name(): string
    {
        return 'staatic_aws_s3';
    }

    public function type(): string
    {
        return self::TYPE_COMPOSED;
    }

    public function label(): string
    {
        return __('S3', 'staatic');
    }

    public function settings(): array
    {
        return [
            $this->settingLocator->get(EndpointSetting::class),
            $this->settingLocator->get(S3RegionSetting::class),
            $this->settingLocator->get(S3BucketSetting::class),
            $this->settingLocator->get(S3PrefixSetting::class),
            $this->settingLocator->get(S3ObjectAcl::class),
            $this->settingLocator->get(RetainPathsSetting::class)
        ];
    }
}
