<?php

use Staatic\WordPress\Bootstrap;
use Staatic\WordPress\Premium\Activator;
use Staatic\WordPress\Premium\Deactivator;

require __DIR__ . '/vendor/autoload.php';

function staatic_activate($networkWide)
{
    $bootstrap = Bootstrap::instance();

    $activate = function (bool $reload = false) use ($bootstrap) {
        $container = $reload ? $bootstrap->reloadContainer() : $bootstrap->container();
        if ($container->has(Activator::class)) {
            $container->get(Activator::class)->activate();
        }
        $container->get(Staatic\WordPress\Activator::class)->activate();
    };

    if (is_multisite() && $networkWide) {
        foreach (get_sites([
            'fields' => 'ids'
        ]) as $blogId) {
            switch_to_blog($blogId);
            $activate(true);
        }
        restore_current_blog();
    } else {
        $activate();
    }
}

function staatic_deactivate($networkWide)
{
    $bootstrap = Bootstrap::instance();

    $deactivate = function (bool $reload = false) use ($bootstrap) {
        $container = $reload ? $bootstrap->reloadContainer() : $bootstrap->container();
        if ($container->has(Deactivator::class)) {
            $container->get(Deactivator::class)->deactivate();
        }
        $container->get(Staatic\WordPress\Deactivator::class)->deactivate();
    };

    if (is_multisite() && $networkWide) {
        foreach (get_sites([
            'fields' => 'ids'
        ]) as $blogId) {
            switch_to_blog($blogId);
            $deactivate(true);
        }
        restore_current_blog();
    } else {
        $deactivate();
    }
}

\register_activation_hook(STAATIC_FILE, 'staatic_activate');
\register_deactivation_hook(STAATIC_FILE, 'staatic_deactivate');

function staatic_initialize()
{
    Bootstrap::instance();
}

// Load early to allow other plugins to access the container.
\add_action('plugins_loaded', 'staatic_initialize', 1);

function staatic_run()
{
    Bootstrap::instance()->loadModules();
}

// Run late to allow other plugins to do their thing.
\add_action('plugins_loaded', 'staatic_run', 100);
