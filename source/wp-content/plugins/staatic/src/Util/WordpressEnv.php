<?php

declare(strict_types=1);

namespace Staatic\WordPress\Util;

use WPML_URL_Filters;

final class WordpressEnv
{
    /**
     * Examples:
     * - https://example.com
     */
    public static function getBaseUrl(): string
    {
        return untrailingslashit(self::wrap(function () {
            return home_url();
        }));
    }

    /**
     * Examples:
     * - /
     */
    public static function getBaseUrlPath(): string
    {
        return wp_parse_url(self::getBaseUrl(), \PHP_URL_PATH) ?? '/';
    }

    /**
     * Examples:
     * - /var/www/html/wordpress
     */
    public static function getWordpressPath(): string
    {
        return wp_normalize_path(untrailingslashit(\ABSPATH));
    }

    /**
     * Examples:
     * - https://example.com
     * - https://example.com/wordpress
     */
    public static function getWordpressUrl(): string
    {
        return untrailingslashit(site_url());
    }

    /**
     * Examples:
     * - /
     * - /wordpress
     */
    public static function getWordpressUrlPath(): string
    {
        return wp_parse_url(self::getWordpressUrl(), \PHP_URL_PATH) ?? '/';
    }

    /**
     * Examples:
     * - /var/www/html/wp-content
     */
    public static function getContentPath(): string
    {
        return wp_normalize_path(untrailingslashit(\WP_CONTENT_DIR));
    }

    /**
     * Examples:
     * - https://example.com/wp-content
     */
    public static function getContentUrl(): string
    {
        return untrailingslashit(content_url());
    }

    /**
     * Examples:
     * - /wp-content
     */
    public static function getContentUrlPath(): string
    {
        return wp_parse_url(self::getContentUrl(), \PHP_URL_PATH);
    }

    /**
     * Examples:
     * - /var/www/html/wp-includes
     */
    public static function getIncludesPath(): string
    {
        return wp_normalize_path(untrailingslashit(\ABSPATH . \WPINC));
    }

    /**
     * Examples:
     * - https://example.com/wp-includes
     */
    public static function getIncludesUrl(): string
    {
        return untrailingslashit(includes_url());
    }

    /**
     * Examples:
     * - /wp-includes
     */
    public static function getIncludesUrlPath(): string
    {
        return wp_parse_url(self::getIncludesUrl(), \PHP_URL_PATH);
    }

    /**
     * Examples:
     * - /var/www/html/wp-content/uploads
     */
    public static function getUploadsPath(): string
    {
        return wp_normalize_path(untrailingslashit(wp_upload_dir(null, \false)['basedir']));
    }

    /**
     * Examples:
     * - https://example.com/wp-content/uploads
     * - https://example.com/wp-content/uploads/sites/1
     */
    public static function getUploadsUrl(): string
    {
        return untrailingslashit(wp_upload_dir(null, \false)['baseurl']);
    }

    /**
     * Examples:
     * - /wp-content/uploads
     * - /wp-content/uploads/sites/1
     */
    public static function getUploadsUrlPath(): string
    {
        return wp_parse_url(self::getUploadsUrl(), \PHP_URL_PATH);
    }

    /**
     * Examples:
     * - /var/www/html/wp-content/debug.log
     * - /var/www/html/debug.log
     * - /var/log/wordpress/debug.log
     */
    public static function getDebugLogPath(): string
    {
        if (defined('WP_DEBUG_LOG') && is_string(\WP_DEBUG_LOG)) {
            return wp_normalize_path(\WP_DEBUG_LOG);
        }

        return wp_normalize_path(\WP_CONTENT_DIR . '/debug.log');
    }

    /**
     * Examples:
     * - https://example.com/wp-content/debug.log
     * - https://example.com/debug.log
     * - NULL
     */
    public static function getDebugLogUrl(): ?string
    {
        if (!defined('WP_DEBUG_LOG') || !is_string(\WP_DEBUG_LOG)) {
            return self::getContentUrl() . '/debug.log';
        }
        $wordpressPath = self::getWordpressPath();
        $debugLogPath = self::getDebugLogPath();
        if (strpos($debugLogPath, $wordpressPath) === false) {
            return null;
        }

        return self::getWordpressUrl() . str_replace($wordpressPath, '', $debugLogPath);
    }

    /**
     * Examples:
     * - /wp-content/debug.log
     * - /debug.log
     * - NULL
     */
    public static function getDebugLogUrlPath(): ?string
    {
        $debugLogUrl = self::getDebugLogUrl();
        if ($debugLogUrl === null) {
            return null;
        }

        return wp_parse_url($debugLogUrl, \PHP_URL_PATH);
    }

    public static function getUrlsEndWithSlash(): bool
    {
        $structure = get_option('permalink_structure');

        return $structure && substr_compare($structure, '/', -strlen('/')) === 0;
    }

    private static function wrap(callable $fn, ...$args)
    {
        global $wpml_url_filters;
        if (!$wpml_url_filters instanceof WPML_URL_Filters) {
            return $fn(...$args);
        }
        // Ensure that no (WPML) language code is appended to the URL.
        $wpml_url_filters->remove_global_hooks();
        $result = $fn(...$args);
        $wpml_url_filters->add_global_hooks();

        return $result;
    }
}
