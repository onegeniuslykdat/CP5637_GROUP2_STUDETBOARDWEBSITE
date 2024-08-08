<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Service\SiteUrlProvider;
use Staatic\WordPress\Setting\AbstractSetting;

final class PageNotFoundPathSetting extends AbstractSetting
{
    /**
     * @var SiteUrlProvider
     */
    private $siteUrlProvider;

    public function __construct(SiteUrlProvider $siteUrlProvider)
    {
        $this->siteUrlProvider = $siteUrlProvider;
    }

    public function name(): string
    {
        return 'staatic_page_not_found_path';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Page Not Found Path', 'staatic');
    }

    public function description(): ?string
    {
        return __('This should be any path generating a 404 page not found page, e.g. /404_not_found/.', 'staatic');
    }

    public function defaultValue()
    {
        return rtrim($this->basePath(), '/') . '/404_not_found/';
    }

    private function basePath(): string
    {
        return ($this->siteUrlProvider)()->getPath();
    }
}
