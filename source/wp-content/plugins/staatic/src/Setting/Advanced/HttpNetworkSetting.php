<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\ComposedSettingInterface;

final class HttpNetworkSetting extends AbstractSetting implements ComposedSettingInterface
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
        return 'staatic_http_network';
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
        return [
            $this->settingLocator->get(HttpTimeoutSetting::class),
            $this->settingLocator->get(HttpDelaySetting::class),
            $this->settingLocator->get(HttpConcurrencySetting::class),
            $this->settingLocator->get(HttpToHttpsSetting::class)
        ];
    }
}
