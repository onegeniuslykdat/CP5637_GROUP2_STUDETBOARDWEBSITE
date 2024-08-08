<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Service\PartialRenderer;
use Staatic\WordPress\Service\Settings;

final class SettingsPage implements ModuleInterface
{
    /**
     * @var AdminNavigation
     */
    private $navigation;

    /**
     * @var PartialRenderer
     */
    private $renderer;

    /**
     * @var Settings
     */
    private $settings;

    /** @var string */
    public const PAGE_SLUG = 'staatic-settings';

    public function __construct(AdminNavigation $navigation, PartialRenderer $renderer, Settings $settings)
    {
        $this->navigation = $navigation;
        $this->renderer = $renderer;
        $this->settings = $settings;
    }

    public function hooks(): void
    {
        if (!is_admin()) {
            return;
        }
        add_action('init', [$this, 'addMenuItem']);
        add_action('admin_init', [$this->settings, 'settingsApiInit']);
    }

    public function addMenuItem(): void
    {
        $this->navigation->addMenuItem(
            __('Settings', 'staatic'),
            __('Staatic Settings', 'staatic'),
            self::PAGE_SLUG,
            [$this, 'render'],
            'staatic_manage_settings',
            null,
            0
        );
    }

    public function render(): void
    {
        $groups = $this->settings->groups();
        $groupNames = array_keys($groups);
        $currentGroupName = (isset($_REQUEST['group']) && in_array(
            $_REQUEST['group'],
            $groupNames,
            \true
        )) ? $_REQUEST['group'] : $groupNames[0];
        $errors = $this->settings->renderErrors();
        $hiddenFields = $this->settings->renderHiddenFields($currentGroupName);
        $settings = $this->settings->renderSettings($currentGroupName);
        $hasSettings = $this->settings->hasSettings($currentGroupName);
        $this->renderer->render(
            'admin/settings.php',
            compact('groups', 'currentGroupName', 'errors', 'hiddenFields', 'settings', 'hasSettings')
        );
    }
}
