<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\ComposedSettingInterface;

final class AuthSetting extends AbstractSetting implements ComposedSettingInterface
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
        return 'staatic_aws_auth';
    }

    public function type(): string
    {
        return self::TYPE_COMPOSED;
    }

    public function label(): string
    {
        return __('Authentication', 'staatic');
    }

    public function description(): ?string
    {
        return sprintf(
            /* translators: 1: Link to AWS Documentation. */
            __('In order to authenticate using <a href="%1$s" target="blank">a credentials file and profile</a>, supply the name of the Profile (preferred),<br><strong>or</strong> in order to authenticate directly, supply the Access Key ID and Secret Access Key.<br>Leave both empty to use IAM security credentials from AWS EC2 instances or AWS ECS containers.', 'staatic'),
            'https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials_profiles.html',
            'https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials_hardcoded.html'
        );
    }

    public function settings(): array
    {
        return [
            $this->settingLocator->get(AuthProfileSetting::class),
            $this->settingLocator->get(AuthAccessKeyIdSetting::class),
            $this->settingLocator->get(AuthSecretAccessKeySetting::class)
        ];
    }
}
