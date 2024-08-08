<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use Staatic\Crawler\CrawlUrlProvider\AdditionalPathCrawlUrlProvider\AdditionalPath;
use Staatic\WordPress\Util\WordpressEnv;
use WP_Error;

class AdditionalPaths
{
    /**
     * @param string|null $value
     */
    public static function validateAndResolve($value): array
    {
        $value = self::convertLegacyFormat($value);
        $wordpressPath = wp_normalize_path(WordpressEnv::getWordpressPath());
        $wordpressUrlPath = WordpressEnv::getWordpressUrlPath();
        $newValue = $resolvedValue = [];
        $errors = new WP_Error();
        foreach (explode("\n", (string) $value) as $line) {
            if (!($line = trim($line)) || strncmp($line, '#', strlen('#')) === 0) {
                $newValue[] = $line;

                continue;
            }
            [$path, $uriBasePath, $flags] = array_pad(str_getcsv($line, ' '), 3, null);
            if (!$path) {
                continue;
            }
            if (!self::isAbsolutePath($path)) {
                $errors->add('invalid_additional_path', sprintf(
                    /* translators: 1: Additional path. */
                    __('Path "%1$s" should be absolute.', 'staatic'),
                    esc_html($path)
                ));
                $newValue[] = "# {$line}";

                continue;
            }
            if (realpath($path) === \false) {
                $errors->add('invalid_additional_path', sprintf(
                    /* translators: 1: Additional path. */
                    __('Path "%1$s" is not readable.', 'staatic'),
                    esc_html($path)
                ));
                $newValue[] = "# {$line}";

                continue;
            }
            $normalizedPath = wp_normalize_path($path);
            if (untrailingslashit($normalizedPath) === untrailingslashit($wordpressPath)) {
                $errors->add('invalid_additional_path', sprintf(
                    /* translators: 1: Additional path. */
                    __('Path "%1$s" is the WordPress installation directory.', 'staatic'),
                    esc_html($path)
                ));
                $newValue[] = "# {$line}";

                continue;
            }
            if (!$uriBasePath) {
                $uriBasePath = AdditionalPath::resolveUriBasePath($normalizedPath, $wordpressPath, $wordpressUrlPath);
            }
            if (!$uriBasePath) {
                $errors->add('invalid_additional_path', sprintf(
                    /* translators: 1: Additional path. */
                    __('Path "%1$s" is not convertible to a URL path.', 'staatic'),
                    esc_html($path)
                ));
                $newValue[] = "# {$line}";

                continue;
            }
            $newValue[] = $line;
            $dontTouch = $flags && strpos($flags, 'T') !== false;
            $dontFollow = $flags && strpos($flags, 'F') !== false;
            $dontSave = $flags && strpos($flags, 'S') !== false;
            $nonRecursive = $flags && strpos($flags, 'R') !== false;
            $resolvedValue[$normalizedPath] = new AdditionalPath(
                $normalizedPath,
                $uriBasePath,
                'normal',
                $dontTouch,
                $dontFollow,
                $dontSave,
                !$nonRecursive
            );
        }

        return [
            'newValue' => implode("\n", $newValue),
            'resolvedValue' => array_values($resolvedValue),
            'errors' => $errors
        ];
    }

    private static function isAbsolutePath(string $path): bool
    {
        return strncmp($path, '/', strlen('/')) === 0 || preg_match('~^[A-Z]:/~', $path) === 1;
    }

    /**
     * @param string|null $value
     */
    public static function validate($value): WP_Error
    {
        return self::validateAndResolve($value)['errors'];
    }

    /** @return AdditionalPath[]
     * @param string|null $value */
    public static function resolve($value): array
    {
        return self::validateAndResolve($value)['resolvedValue'];
    }

    /**
     * @param string|null $value
     */
    public static function convertLegacyFormat($value): ?string
    {
        return ($value === null) ? null : preg_replace('~^(\s*#\s*)?(\S+)\s+([FSTR]+)\s*$~m', '$1$2  $3', $value);
    }
}
