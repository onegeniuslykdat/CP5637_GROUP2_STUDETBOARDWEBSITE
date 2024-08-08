<?php

use Staatic\WordPress\Premium\Uninstaller;

// If uninstall not called from WordPress, then exit.

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

function staatic_uninstallers()
{
    global $wpdb;

    $uninstallers = [];

    if (file_exists(__DIR__ . '/premium/src/Uninstaller.php')) {
        require __DIR__ . '/premium/src/Uninstaller.php';

        $uninstallers[] = new Uninstaller($wpdb);
    }

    require __DIR__ . '/src/Uninstaller.php';

    $uninstallers[] = new Staatic\WordPress\Uninstaller($wpdb);

    return $uninstallers;
}

function staatic_uninstall()
{
    $uninstallers = staatic_uninstallers();

    if (is_multisite()) {
        foreach (get_sites([
            'fields' => 'ids'
        ]) as $blogId) {
            switch_to_blog($blogId);
            foreach ($uninstallers as $uninstaller) {
                $uninstaller->uninstall();
            }
        }
        restore_current_blog();
    } else {
        foreach ($uninstallers as $uninstaller) {
            $uninstaller->uninstall();
        }
    }
}

staatic_uninstall();
