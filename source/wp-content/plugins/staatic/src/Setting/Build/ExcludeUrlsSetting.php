<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Build;

use Staatic\WordPress\Service\ExcludeUrls;
use Staatic\WordPress\Service\SiteUrlProvider;
use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Util\CsvUtil;

final class ExcludeUrlsSetting extends AbstractSetting
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
        return 'staatic_exclude_urls';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Excluded URLs', 'staatic');
    }

    public function description(): ?string
    {
        $examples = array_map(function (string $example) {
            return rtrim($this->basePath(), '/') . $example;
        }, ['/excluded-page/', '/excluded-page/*', '~^/excluded-page-[1-3]/~']);

        return sprintf(
            /* translators: %s: Example exclude URLs. */
            __('Optionally add URLs that need to be excluded from the build.<br>%s', 'staatic'),
            $this->examplesList($examples)
        );
    }

    public function defaultValue()
    {
        return implode("\n", array_map(function (string $url) {
            return CsvUtil::strPutCsv([$url], ' ');
        }, [rtrim($this->basePath(), '/') . '/wp-json/*']));
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        $this->renderer->render('admin/settings/excluded_urls.php', [
            'setting' => $this,
            'attributes' => $attributes
        ]);
    }

    public function sanitizeValue($value)
    {
        $result = ExcludeUrls::validateAndResolve($value, ($this->siteUrlProvider)());
        foreach ($result['errors']->get_error_messages() as $message) {
            add_settings_error('staatic-settings', 'exclude_urls', __('Skipped: ', 'staatic') . $message);
        }

        return $result['newValue'];
    }

    private function basePath(): string
    {
        return ($this->siteUrlProvider)()->getPath();
    }
}
