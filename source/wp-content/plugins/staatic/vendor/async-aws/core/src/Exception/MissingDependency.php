<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Exception;

use RuntimeException;
class MissingDependency extends RuntimeException implements Exception
{
    /**
     * @param string $package
     * @param string $name
     */
    public static function create($package, $name)
    {
        return new self(sprintf('In order to use "%s" you need to install "%s". Run "composer require %s" and all your problems are solved.', $name, $package, $package));
    }
}
