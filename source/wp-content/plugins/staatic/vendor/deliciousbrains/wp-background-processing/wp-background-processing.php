<?php

namespace Staatic\Vendor;

if (!\class_exists('Staatic\Vendor\WP_Async_Request')) {
    require_once \plugin_dir_path(__FILE__) . 'classes/wp-async-request.php';
}
if (!\class_exists('Staatic\Vendor\WP_Background_Process')) {
    require_once \plugin_dir_path(__FILE__) . 'classes/wp-background-process.php';
}
