<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

use Staatic\WordPress\Migrations\MigrationCoordinatorFactory;
use WP_Error;

class EnsureMigrated implements ModuleInterface
{
    /**
     * @var MigrationCoordinatorFactory
     */
    private $coordinatorFactory;

    /**
     * @var string
     */
    protected $namespace = 'staatic';

    public function __construct(MigrationCoordinatorFactory $coordinatorFactory)
    {
        $this->coordinatorFactory = $coordinatorFactory;
    }

    public function hooks(): void
    {
        if (!is_admin()) {
            return;
        }
        add_action('init', [$this, 'migrator'], 100);
    }

    public function migrator(): void
    {
        $coordinator = ($this->coordinatorFactory)($this->namespace);
        $action = $_GET['staatic'] ?? null;
        $retrying = $resetting = \false;
        if ($action === "_reset_{$this->namespace}") {
            check_admin_referer("staatic-reset_{$this->namespace}");
            $resetting = \true;
        } elseif ($action === "_migrate_{$this->namespace}") {
            check_admin_referer("staatic-migrate_{$this->namespace}");
            $retrying = \true;
        }
        if ($coordinator->hasMigrationFailed() && !$retrying && !$resetting) {
            $status = $coordinator->status();
            $this->handleMigrationError($status['error']['message'], $status['version'], $status['error']['version']);
        } elseif ($coordinator->isMigrating()) {
            wp_die(new WP_Error('locked', sprintf(
                /* translators: 1: Plugin Name. */
                __('The %1$s database is being upgraded; please try again later.', 'staatic'),
                $this->pluginName()
            )));
        } elseif ($coordinator->shouldMigrate()) {
            if ($resetting) {
                $coordinator->reset();
            } else {
                $coordinator->migrate();
            }
        }
        if ($retrying || $resetting) {
            wp_redirect(admin_url());
            exit;
        }
    }

    /**
     * @param string $message
     * @param string $sourceVersion
     * @param string $targetVersion
     */
    protected function handleMigrationError($message, $sourceVersion, $targetVersion): void
    {
        add_action('admin_notices', function () use ($message, $sourceVersion, $targetVersion) {
            echo '<div class="error">';
            echo '<p><strong>' . sprintf(
                /* translators: 1: Plugin Name. */
                __('%1$s was unable to upgrade the database.', 'staatic'),
                $this->pluginName()
            ) . '</strong></p>';
            echo '<p>' . sprintf(
                /* translators: 1: Link to Retry, 2: Link to Contact. */
                __('Please try the upgrade again by <a href="%1$s">clicking here</a>. If the problem persists, <a href="%2$s" target="_blank" rel="noopener">contact Staatic support</a> and provide the following details:', 'staatic'),
                wp_nonce_url(
                    admin_url("admin.php?staatic=_migrate_{$this->namespace}"),
                    "staatic-migrate_{$this->namespace}"
                ),
                'https://staatic.com/wordpress/contact/'
            ) . '</p>';
            echo '<dl>';
            echo '<dt>' . esc_html__('Current version', 'staatic') . '</dt><dd>' . esc_html($sourceVersion) . '</dd>';
            echo '<dt>' . esc_html__('Target version', 'staatic') . '</dt><dd>' . esc_html($targetVersion) . '</dd>';
            echo '<dt>' . esc_html__('Error', 'staatic') . '</dt><dd>' . esc_html($message) . '</dd>';
            echo '</dl>';
            echo '<p>' . sprintf(
                /* translators: 1: Reset link. */
                __('Alternatively, you can %1$s to recreate the plugin\'s tables, which will also delete any existing publication data.', 'staatic'),
                sprintf(
                    '<a href="%1$s" onclick="return confirm(\'%2$s\');">%3$s</a>',
                    wp_nonce_url(
                        admin_url("admin.php?staatic=_reset_{$this->namespace}"),
                        "staatic-reset_{$this->namespace}"
                    ),
                    esc_js(
                        __('This will delete all past publication data. Are you sure that you want to reset the database?', 'staatic')
                    ),
                    __('reset the database', 'staatic')
                )
            ) . '</p>';
            echo '</div>';
        });
    }

    protected function pluginName(): string
    {
        return __('Staatic', 'staatic');
    }

    public static function getDefaultPriority(): int
    {
        return \PHP_INT_MAX;
    }
}
