<?php

declare(strict_types=1);

namespace Staatic\WordPress;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\WordPress\DependencyInjection\CachedContainer;
use Staatic\WordPress\DependencyInjection\ContainerCompiler;
use Staatic\WordPress\Module\ModuleCollection;

final class Bootstrap
{
    private static $instance = null;

    /**
     * @var bool
     */
    private $isDevMode;

    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @var bool
     */
    private $isPremium;

    /**
     * @var ContainerInterface
     */
    private $container;

    private function __construct()
    {
        $this->isDevMode = (bool) ($_ENV['STAATIC_DEV_MODE'] ?? $_SERVER['STAATIC_DEV_MODE'] ?? \false);
        $this->isDebug = (bool) ($_ENV['STAATIC_DEBUG'] ?? $_SERVER['STAATIC_DEBUG'] ?? \false);
        $this->isPremium = ($_ENV['STAATIC_PREMIUM'] ?? $_SERVER['STAATIC_PREMIUM'] ?? \true) && is_dir(
            __DIR__ . '/../premium'
        );
        $this->container = $this->setupContainer();
    }

    private function setupContainer(): ContainerInterface
    {
        $containerFile = __DIR__ . '/../generated/container.php';
        if ($this->isDevMode() && class_exists(ContainerCompiler::class)) {
            ContainerCompiler::compile($containerFile, $this->isDebug(), $this->isPremium());
        }
        require $containerFile;
        $container = new CachedContainer();

        return $container;
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function container(): ContainerInterface
    {
        return $this->container;
    }

    public function reloadContainer(): ContainerInterface
    {
        $this->container = new CachedContainer();

        return $this->container;
    }

    public function loadModules(): void
    {
        /** @var ModuleCollection $modules */
        $modules = $this->container->get(ModuleCollection::class);
        foreach ($modules as $module) {
            $module->hooks();
        }
    }

    public function isDevMode(): bool
    {
        return $this->isDevMode;
    }

    public function isDebug(): bool
    {
        return apply_filters('staatic_debug', $this->isDebug);
    }

    public function isPremium(): bool
    {
        return apply_filters('staatic_premium', $this->isPremium);
    }
}
