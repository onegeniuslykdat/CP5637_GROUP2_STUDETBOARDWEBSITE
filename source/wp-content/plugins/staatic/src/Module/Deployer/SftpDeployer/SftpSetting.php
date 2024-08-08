<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\SftpDeployer;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\ComposedSettingInterface;

final class SftpSetting extends AbstractSetting implements ComposedSettingInterface
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
        return 'staatic_sftp_server';
    }

    public function type(): string
    {
        return self::TYPE_COMPOSED;
    }

    public function label(): string
    {
        return __('SFTP Server', 'staatic');
    }

    public function settings(): array
    {
        return [
            $this->settingLocator->get(HostSetting::class),
            $this->settingLocator->get(PortSetting::class),
            $this->settingLocator->get(TargetDirectorySetting::class)
        ];
    }
}
