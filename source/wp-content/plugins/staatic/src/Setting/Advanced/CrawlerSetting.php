<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\ComposedSettingInterface;

final class CrawlerSetting extends AbstractSetting implements ComposedSettingInterface
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
        return 'staatic_crawler';
    }

    public function type(): string
    {
        return self::TYPE_COMPOSED;
    }

    public function label(): string
    {
        return __('Crawler', 'staatic');
    }

    public function settings(): array
    {
        return [
            $this->settingLocator->get(OverrideSiteUrlSetting::class),
            $this->settingLocator->get(PageNotFoundPathSetting::class),
            $this->settingLocator->get(CrawlerProcessNotFoundSetting::class),
            $this->settingLocator->get(CrawlerLowercaseUrlsSetting::class),
            $this->settingLocator->get(CrawlerDomParserSetting::class)
        ];
    }
}
