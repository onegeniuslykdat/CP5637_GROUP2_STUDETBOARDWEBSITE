<?php

namespace Staatic\Vendor;

if (!\function_exists('Staatic\Vendor\trigger_deprecation')) {
    /**
     * @param mixed ...$args
     */
    function trigger_deprecation(string $package, string $version, string $message, ...$args): void
    {
        @\trigger_error((($package || $version) ? "Since {$package} {$version}: " : '') . ($args ? \vsprintf($message, $args) : $message), \E_USER_DEPRECATED);
    }
}
