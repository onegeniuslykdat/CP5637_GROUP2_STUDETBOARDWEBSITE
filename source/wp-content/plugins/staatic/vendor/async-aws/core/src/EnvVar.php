<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core;

final class EnvVar
{
    public static function get(string $name): ?string
    {
        if (isset($_ENV[$name])) {
            return (string) $_ENV[$name];
        } elseif (isset($_SERVER[$name]) && !\is_array($_SERVER[$name]) && 0 !== strpos($name, 'HTTP_')) {
            return (string) $_SERVER[$name];
        } elseif (\false === $env = getenv($name)) {
            return null;
        }
        return $env;
    }
}
