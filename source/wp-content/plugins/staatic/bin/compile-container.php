#!/usr/bin/env php
<?php 
namespace Staatic\Vendor;

use Staatic\WordPress\DependencyInjection\ContainerCompiler;

require __DIR__ . '/../vendor/autoload.php';
ContainerCompiler::compile(
    __DIR__ . '/../generated/container.php',
    (bool) ($_SERVER['COMPOSER_DEV_MODE'] ?? \false),
    \true
);
