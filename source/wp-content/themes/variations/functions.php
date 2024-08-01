<?php

/**
 * This file contains functions and hooks.
 *
 * @package Variations
 * 
 */

/**
 * Define version to use it with JS and CSS files.
 */
if (!defined('VARIATIONS_THEME_VERSION')) {

    define('VARIATIONS_THEME_VERSION', wp_get_theme()->get('Version'));
}

/**
 * Enqueue Scripts.
 */
require_once get_template_directory() . '/inc/enqueue-scripts.php';

/**
 * Hooks and actions.
 */
require_once get_template_directory() . '/inc/hooks-actions.php';
