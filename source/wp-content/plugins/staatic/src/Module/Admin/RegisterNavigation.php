<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Service\AdminNavigation;

final class RegisterNavigation implements ModuleInterface
{
    /**
     * @var AdminNavigation
     */
    private $navigation;

    public function __construct(AdminNavigation $navigation)
    {
        $this->navigation = $navigation;
    }

    public function hooks(): void
    {
        if (!is_admin()) {
            return;
        }
        $this->navigation->registerHooks();
    }

    public static function getDefaultPriority(): int
    {
        return 20;
    }
}
