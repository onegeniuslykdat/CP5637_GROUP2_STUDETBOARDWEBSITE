<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\SftpDeployer;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\ComposedSettingInterface;

final class NetworkSetting extends AbstractSetting implements ComposedSettingInterface
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
        return 'staatic_sftp_network';
    }

    public function type(): string
    {
        return self::TYPE_COMPOSED;
    }

    public function label(): string
    {
        return __('Network', 'staatic');
    }

    public function settings(): array
    {
        return [$this->settingLocator->get(TimeoutSetting::class)];
    }
}
