<?php

function staatic_platform_check_php_failure() {
    if (!is_admin()) {
        return;
    }

    add_action('admin_notices', 'staatic_platform_check_php_notice');
    staatic_platform_check_deactivate();
}

function staatic_platform_check_wordpress_failure() {
    if (!is_admin()) {
        return;
    }

    add_action('admin_notices', 'staatic_platform_check_wordpress_notice');
    staatic_platform_check_deactivate();
}

function staatic_platform_check_key_failure() {
    if (!is_admin()) {
        return;
    }

    add_action('admin_notices', 'staatic_platform_check_key_notice');
    staatic_platform_check_deactivate();
}

function staatic_platform_check_php_notice() {
    return staatic_platform_check_notice(
        sprintf(
            /* translators: 1: Minimum version, 2: Installed version. */
            __('<p>Staatic for WordPress requires at least PHP version <strong>%1$s</strong>; detected PHP version <strong>%2$s</strong>.</p>', 'staatic'),
            esc_html(STAATIC_MINIMUM_PHP_VERSION),
            esc_html(PHP_VERSION)
        )
    );
}

function staatic_platform_check_wordpress_notice() {
    global $wp_version;

    return staatic_platform_check_notice(
        sprintf(
            /* translators: 1: Minimum version, 2: Installed version. */
            __('<p>Staatic for WordPress requires at least WordPress version <strong>%1$s</strong>; detected WordPress version <strong>%2$s</strong>.</p>', 'staatic'),
            esc_html(STAATIC_MINIMUM_WORDPRESS_VERSION),
            esc_html($wp_version)
        )
    );
}

function staatic_platform_check_key_notice() {
    return staatic_platform_check_notice(
        sprintf(
            /* translators: 1: Code fragment. */
            __('<p>Staatic for WordPress requires either <code>STAATIC_KEY</code> or <code>AUTH_KEY</code> to be defined within <code>wp-config.php</code>. In order to fix this error, please add the following line to your <code>wp-config.php</code> file and reactivate the plugin:</p><p><code>%1$s</code></p>', 'staatic'),
            esc_html(sprintf('define(\'STAATIC_KEY\', \'%s\');', wp_generate_password(64, true, true)))
        )
    );
}

function staatic_platform_check_notice($message) {
    echo '<div class="error"><p>';
    echo '<p><strong>', esc_html__('Plugin activation failed', 'staatic'), '</strong></p> ', $message;
    echo '</p></div>';
}

function staatic_platform_check_deactivate() {
    static $isDeactivated = false;

    if ($isDeactivated) {
        return;
    }

    $isDeactivated = true;

    deactivate_plugins(plugin_basename(STAATIC_FILE));

    if (isset($_GET['activate'])) {
        unset($_GET['activate']);
    }
}

$platformCheckSuccessful = true;

if (version_compare(PHP_VERSION, STAATIC_MINIMUM_PHP_VERSION, '<')) {
    add_action('admin_init', 'staatic_platform_check_php_failure', 1);
    $platformCheckSuccessful = false;
}

global $wp_version;
if (version_compare($wp_version, STAATIC_MINIMUM_WORDPRESS_VERSION, '<')) {
    add_action('admin_init', 'staatic_platform_check_wordpress_failure', 1);
    $platformCheckSuccessful = false;
}

if (!defined('STAATIC_KEY') && !defined('AUTH_KEY')) {
    add_action('admin_init', 'staatic_platform_check_key_failure', 1);
    $platformCheckSuccessful = false;
}

return $platformCheckSuccessful;
