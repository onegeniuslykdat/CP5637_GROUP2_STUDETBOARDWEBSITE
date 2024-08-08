<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use Closure;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
interface EnvVarProcessorInterface
{
    /**
     * @param string $prefix
     * @param string $name
     * @param Closure $getEnv
     * @return mixed
     */
    public function getEnv($prefix, $name, $getEnv);
    public static function getProvidedTypes(): array;
}
