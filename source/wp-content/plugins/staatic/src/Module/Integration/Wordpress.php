<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Integration;

use Staatic\Crawler\CrawlUrlProvider\AdditionalUrlCrawlUrlProvider\AdditionalUrl;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Util\WordpressEnv;

final class Wordpress implements ModuleInterface
{
    public function hooks(): void
    {
        add_action('wp_loaded', [$this, 'setupIntegration']);
    }

    public function setupIntegration(): void
    {
        add_filter('staatic_additional_urls', [$this, 'overrideAdditionalUrls']);
        add_filter('staatic_additional_paths_exclude_paths', [$this, 'overrideExcludePaths']);
    }

    /** @param AdditionalUrl[] $additionalUrls */
    public function overrideAdditionalUrls($additionalUrls): array
    {
        return array_merge($additionalUrls, $this->determineEmojiUrls());
    }

    /** @param AdditionalUrl[] $additionalUrls */
    private function determineEmojiUrls(): array
    {
        $includesPath = WordpressEnv::getIncludesPath();
        $includesUrlPath = WordpressEnv::getIncludesUrlPath();
        $candidatePaths = [
            "{$includesPath}/js/wp-emoji-release.min.js" => "{$includesUrlPath}/js/wp-emoji-release.min.js",
            "{$includesPath}/js/wp-emoji.js" => "{$includesUrlPath}/js/wp-emoji.js",
            "{$includesPath}/js/twemoji.js" => "{$includesUrlPath}/js/twemoji.js"
        ];

        return array_map(function (string $url) {
            return new AdditionalUrl($url);
        }, array_filter($candidatePaths, function (string $path) {
            return file_exists($path);
        }, \ARRAY_FILTER_USE_KEY));
    }

    /** @param string[] $excludePaths */
    public function overrideExcludePaths($excludePaths): array
    {
        return array_merge($excludePaths, $this->determineExcludeRules());
    }

    private function determineExcludeRules(): array
    {
        $uploadsDirectories = [
            'et_temp',
            'exported_html_files',
            'file-manager',
            'ithemes-security',
            'simply-static',
            'sucuri',
            'wc-logs',
            'wp-activity-log',
            'wp-file-manager-pro',
            'wp-security-audit-log',
            'wp2static-crawled-site',
            'wpallexport',
            'wpallimport',
            'wpcf7_uploads',
            'wpcode',
            'wpfc-backup'
        ];
        $uploadsPaths = [$uploadsPath = WordpressEnv::getUploadsPath()];
        $realUploadsPath = realpath($uploadsPath);
        $realUploadsPath = $realUploadsPath ? wp_normalize_path($realUploadsPath) : null;
        if ($realUploadsPath && $realUploadsPath !== $uploadsPath) {
            $uploadsPaths[] = $realUploadsPath;
        }
        $candidatePaths = [];
        foreach ($uploadsDirectories as $directory) {
            foreach ($uploadsPaths as $uploadsPath) {
                $candidatePaths[] = "{$uploadsPath}/{$directory}";
            }
        }
        if (WordpressEnv::getDebugLogUrlPath()) {
            $candidatePaths[] = WordpressEnv::getDebugLogPath();
        }

        return array_filter($candidatePaths, function (string $path) {
            return file_exists($path);
        });
    }
}
