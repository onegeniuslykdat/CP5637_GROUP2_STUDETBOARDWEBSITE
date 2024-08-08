<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

use Staatic\WordPress\Util\HttpUtil;

final class Compatibility implements ModuleInterface
{
    public function hooks(): void
    {
        if (!$this->isCrawlRequest()) {
            return;
        }
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
    }

    private function isCrawlRequest(): bool
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && strpos(
            $_SERVER['HTTP_USER_AGENT'],
            HttpUtil::USER_AGENT_NAME
        ) !== false;
    }
}
