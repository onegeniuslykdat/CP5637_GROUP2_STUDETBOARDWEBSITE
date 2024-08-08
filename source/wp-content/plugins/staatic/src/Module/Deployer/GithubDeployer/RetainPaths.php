<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\GithubDeployer;

use WP_Error;

class RetainPaths
{
    /**
     * @param string|null $value
     * @param string|null $repositoryPrefix
     */
    public static function validateAndResolve($value, $repositoryPrefix): array
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
            $resolvedPath = strncmp($path, '/', strlen('/')) === 0 ? substr($path, 1) : ($repositoryPrefix ? sprintf(
                '%s/%s',
                untrailingslashit($repositoryPrefix),
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
     * @param string|null $repositoryPrefix
     */
    public static function validate($value, $repositoryPrefix): WP_Error
    {
        return self::validateAndResolve($value, $repositoryPrefix)['errors'];
    }

    /** @return string[]
     * @param string|null $value
     * @param string|null $repositoryPrefix */
    public static function resolve($value, $repositoryPrefix): array
    {
        return self::validateAndResolve($value, $repositoryPrefix)['resolvedValue'];
    }
}
