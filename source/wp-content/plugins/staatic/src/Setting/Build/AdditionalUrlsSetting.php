<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Build;

use Staatic\WordPress\Service\AdditionalUrls;
use Staatic\WordPress\Service\SiteUrlProvider;
use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Util\CsvUtil;

final class AdditionalUrlsSetting extends AbstractSetting
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
        return 'staatic_additional_urls';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Additional URLs', 'staatic');
    }

    public function description(): ?string
    {
        $examples = array_map(function (string $example) {
            return rtrim($this->basePath(), '/') . $example;
        }, ['/favicon.ico', '/robots.txt', '/wp-sitemap.xml']);

        return sprintf(
            /* translators: %s: Example additional URLs. */
            __('Optionally add (absolute or relative) URLs that need to be included in the build.<br>%s', 'staatic'),
            $this->examplesList($examples)
        );
    }

    public function defaultValue()
    {
        $urls = [rtrim($this->basePath(), '/') . '/wp-sitemap.xml'];
        if (!$this->basePath() || $this->basePath() === '/') {
            $urls[] = '/robots.txt';
        }

        return implode("\n", array_map(function (string $url) {
            return CsvUtil::strPutCsv([$url], ' ');
        }, $urls));
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        $this->renderer->render('admin/settings/additional_urls.php', [
            'setting' => $this,
            'attributes' => $attributes
        ]);
    }

    public function sanitizeValue($value)
    {
        $result = AdditionalUrls::validateAndResolve($value, ($this->siteUrlProvider)());
        foreach ($result['errors']->get_error_messages() as $message) {
            add_settings_error('staatic-settings', 'additional_urls', __('Skipped: ', 'staatic') . $message);
        }

        return $result['newValue'];
    }

    private function basePath(): string
    {
        return ($this->siteUrlProvider)()->getPath();
    }
}
