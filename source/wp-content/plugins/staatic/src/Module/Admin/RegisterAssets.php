<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin;

use Staatic\WordPress\Module\ModuleInterface;

final class RegisterAssets implements ModuleInterface
{
    /**
     * @var string
     */
    private $pluginPath;

    /**
     * @var string
     */
    private $pluginUrl;

    /**
     * @var string
     */
    private $pluginVersion;

    public function __construct(string $pluginVersion)
    {
        $this->pluginPath = \STAATIC_PATH;
        $this->pluginUrl = \STAATIC_URL;
        $this->pluginVersion = $pluginVersion;
    }

    public function hooks(): void
    {
        if (!is_admin()) {
            return;
        }
        add_action('admin_enqueue_scripts', [$this, 'enqueueStyles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    public function enqueueStyles(): void
    {
        wp_enqueue_style('staatic-admin', "{$this->pluginUrl}/assets/admin.css", [], $this->pluginVersion, 'all');
    }

    public function enqueueScripts(): void
    {
        $scriptAsset = require "{$this->pluginPath}/assets/admin.asset.php";
        wp_enqueue_script(
            'staatic-admin',
            "{$this->pluginUrl}/assets/admin.js",
            $scriptAsset['dependencies'],
            $scriptAsset['version']
        );
        wp_set_script_translations('staatic-admin', 'staatic', plugin_dir_path(\STAATIC_FILE) . 'languages/');
    }

    public static function getDefaultPriority(): int
    {
        return 20;
    }
}
