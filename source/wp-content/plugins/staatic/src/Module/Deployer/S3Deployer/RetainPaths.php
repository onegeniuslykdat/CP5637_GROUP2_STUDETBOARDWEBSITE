<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use WP_Error;

class RetainPaths
{
    /**
     * @param string|null $value
     * @param string|null $bucketPrefix
     */
    public static function validateAndResolve($value, $bucketPrefix): array
    {
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
            $newValue[] = $line;
            $resolvedPath = strncmp($path, '/', strlen('/')) === 0 ? substr($path, 1) : ($bucketPrefix ? sprintf(
                '%s/%s',
                untrailingslashit($bucketPrefix),
                $path
            ) : $path);
            $resolvedValue[$resolvedPath] = wp_normalize_path($resolvedPath);
        }

        return [
            'newValue' => implode("\n", $newValue),
            'resolvedValue' => array_values($resolvedValue),
            'errors' => $errors
        ];
    }

    /**
     * @param string|null $value
     * @param string|null $bucketPrefix
     */
    public static function validate($value, $bucketPrefix): WP_Error
    {
        return self::validateAndResolve($value, $bucketPrefix)['errors'];
    }

    /** @return string[]
     * @param string|null $value
     * @param string|null $bucketPrefix */
    public static function resolve($value, $bucketPrefix): array
    {
        return self::validateAndResolve($value, $bucketPrefix)['resolvedValue'];
    }
}
