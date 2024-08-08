<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\ComposedSettingInterface;

final class HttpAuthenticationSetting extends AbstractSetting implements ComposedSettingInterface
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
        return 'staatic_http_auth';
    }

    public function type(): string
    {
        return self::TYPE_COMPOSED;
    }

    public function label(): string
    {
        return __('HTTP Authentication', 'staatic');
    }

    public function description(): ?string
    {
        return __('If your WordPress installation is protected with HTTP authentication, enter the relevant credentials.', 'staatic');
    }

    public function settings(): array
    {
        return [
            $this->settingLocator->get(HttpAuthenticationUsernameSetting::class),
            $this->settingLocator->get(HttpAuthenticationPasswordSetting::class)
        ];
    }
}
