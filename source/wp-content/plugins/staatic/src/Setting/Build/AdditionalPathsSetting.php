<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Build;

use Staatic\Crawler\CrawlUrlProvider\AdditionalPathCrawlUrlProvider\AdditionalPath;
use Staatic\WordPress\Service\AdditionalPaths;
use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Util\CsvUtil;
use Staatic\WordPress\Util\WordpressEnv;

final class AdditionalPathsSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_additional_paths';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Additional Paths', 'staatic');
    }

    public function description(): ?string
    {
        return sprintf(
            /* translators: %s: Example additional paths. */
            __('Optionally add (filesystem) paths that need to be included in the build.<br>%s', 'staatic'),
            $this->examplesList([WordpressEnv::getUploadsPath()])
        );
    }

    public function value()
    {
        return AdditionalPaths::convertLegacyFormat(parent::value());
    }

    public function defaultValue()
    {
        $uploadsPath = WordpressEnv::getUploadsPath();
        $uploadsUrlPath = WordpressEnv::getUploadsUrlPath();
        $resolvedUriPath = AdditionalPath::resolveUriBasePath(
            $uploadsPath,
            WordpressEnv::getWordpressPath(),
            WordpressEnv::getWordpressUrlPath()
        );
        if ($uploadsUrlPath === $resolvedUriPath) {
            return CsvUtil::strPutCsv([$uploadsPath], ' ');
        }

        return CsvUtil::strPutCsv([$uploadsPath, $uploadsUrlPath], ' ');
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        $this->renderer->render('admin/settings/additional_paths.php', [
            'setting' => $this,
            'attributes' => array_merge([
                'rootPath' => WordpressEnv::getWordpressPath(),
                'rootUrlPath' => '/' . trim(WordpressEnv::getWordpressUrlPath(), '/')
            ], $attributes)
        ]);
    }

    public function sanitizeValue($value)
    {
        $result = AdditionalPaths::validateAndResolve($value);
        foreach ($result['errors']->get_error_messages() as $message) {
            add_settings_error('staatic-settings', 'additional_paths', __('Skipped: ', 'staatic') . $message);
        }

        return $result['newValue'];
    }
}
