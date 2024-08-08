<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\FilesystemDeployer;

use WP_Error;

class RetainPaths
{
    /**
     * @param string|null $value
     * @param string $basePath
     */
    public static function validateAndResolve($value, $basePath): array
    {
        $basePath = rtrim($basePath, '\/');
        $newValue = $resolvedValue = [];
        $errors = new WP_Error();
        foreach (explode("\n", (string) $value) as $line) {
            if (!($line = trim($line)) || strncmp($line, '#', strlen('#')) === 0) {
                $newValue[] = $line;

                continue;
            }
            [$path] = array_pad(str_getcsv($line, ' '), 1, null);
            if (!$path) {
                continue;
            }
            $absolutePath = self::isAbsolutePath($path) ? $path : sprintf('%s/%s', $basePath, $path);
            if (!file_exists($absolutePath)) {
                $errors->add('invalid_retain_path', sprintf(
                    /* translators: 1: Retain path. */
                    __('Path "%1$s" does not exist.', 'staatic'),
                    esc_html($path)
                ));
                $newValue[] = "# {$line}";

                continue;
            }
            $newValue[] = $line;
            $resolvedValue[$path] = wp_normalize_path($absolutePath);
        }

        return [
            'newValue' => implode("\n", $newValue),
            'resolvedValue' => array_values($resolvedValue),
            'errors' => $errors
        ];
    }

    /**
     * @param string|null $value
     * @param string $basePath
     */
    public static function validate($value, $basePath): WP_Error
    {
        return self::validateAndResolve($value, $basePath)['errors'];
    }

    /** @return string[]
     * @param string|null $value
     * @param string $basePath */
    public static function resolve($value, $basePath): array
    {
        return self::validateAndResolve($value, $basePath)['resolvedValue'];
    }

    private static function isAbsolutePath(string $path): bool
    {
        return strncmp($path, '/', strlen('/')) === 0 || preg_match('~^[A-Z]:/~', $path) === 1;
    }
}
