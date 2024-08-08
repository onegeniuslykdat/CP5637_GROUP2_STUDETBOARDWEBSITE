<?php
/**
 * The Staatic for WordPress plugin bootstrap file
 *
 * @wordpress-plugin
 * Plugin Name:       Staatic - Static Site Generator
 * Plugin URI:        https://staatic.com/wordpress
 * Description:       Staatic for WordPress allows you to generate a highly optimized static version of your WordPress site.
 * Version:           1.10.4
 * Requires at least: 5.0
 * Requires PHP:      7.1
 * Author:            Team Staatic
 * Author URI:        https://staatic.com/
 * Text Domain:       staatic
 * Domain Path:       /languages
 * License:           BSD-3-Clause
 */

if (!defined('WPINC')) {
    die;
}

if (defined('STAATIC_VERSION')) {
    return;
}

define('STAATIC_VERSION', '1.10.4');

define('STAATIC_FILE', __FILE__);
define('STAATIC_PATH', dirname(__FILE__));
define('STAATIC_URL', plugins_url('', __FILE__));

define('STAATIC_MINIMUM_PHP_VERSION', '7.1.0');
define('STAATIC_MINIMUM_WORDPRESS_VERSION', '5.0.0');

$platformCheckSuccessful = require_once dirname(__FILE__) . '/platform_check.php';

if ($platformCheckSuccessful) {
    require_once dirname(__FILE__) . '/staatic_load.php';
}
