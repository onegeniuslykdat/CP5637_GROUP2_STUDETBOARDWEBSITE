<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

final class AdminNavigation
{
    public const PARENT_SLUG = 'staatic';

    /**
     * @var mixed[]
     */
    private $menuItems = [];

    /**
     * @var mixed[]
     */
    private $pages = [];

    public function addMenuItem(
        string $menuTitle,
        string $pageTitle,
        string $pageSlug,
        $pageRenderCallback,
        string $capability,
        $lazyLoadCallback = null,
        ?int $position = null
    ): void
    {
        $this->menuItems[$pageSlug] = [
            'menuTitle' => $menuTitle,
            'pageTitle' => $pageTitle,
            'callback' => $pageRenderCallback,
            'capability' => $capability,
            'lazyLoadCallback' => $lazyLoadCallback,
            'position' => $position
        ];
    }

    public function addPage(
        string $pageTitle,
        string $pageSlug,
        $pageRenderCallback,
        string $capability,
        ?string $appearAsPageSlug = null,
        $lazyLoadCallback = null
    ): void
    {
        $this->pages[$pageSlug] = [
            'pageTitle' => $pageTitle,
            'callback' => $pageRenderCallback,
            'capability' => $capability,
            'appearAs' => $appearAsPageSlug ?: self::PARENT_SLUG,
            'lazyLoadCallback' => $lazyLoadCallback
        ];
    }

    public function registerHooks(): void
    {
        add_action('admin_menu', [$this, 'adminMenuSetup']);
        add_action('submenu_file', [$this, 'hidePagesFromMenu']);
    }

    public function adminMenuSetup(): void
    {
        add_menu_page(
            __('Staatic', 'staatic'),
            __('Staatic', 'staatic'),
            'staatic_publish',
            self::PARENT_SLUG,
            '',
            $this->inlineLogoSvg(),
            null
        );
        // Do the sorting here instead of passing it to WordPress.
        uasort($this->menuItems, function ($a, $b) {
            return ($a['position'] ?? 100) <=> ($b['position'] ?? 100);
        });
        foreach ($this->menuItems as $pageSlug => $menuItem) {
            $hook = add_submenu_page(
                self::PARENT_SLUG,
                $menuItem['pageTitle'],
                $menuItem['menuTitle'],
                $menuItem['capability'],
                $pageSlug,
                $menuItem['callback']
            );
            if ($menuItem['lazyLoadCallback']) {
                add_action('load-' . $hook, $menuItem['lazyLoadCallback']);
            }
        }
        foreach ($this->pages as $pageSlug => $page) {
            $hook = add_submenu_page(
                self::PARENT_SLUG,
                $page['pageTitle'],
                '',
                $page['capability'],
                $pageSlug,
                $page['callback']
            );
            if ($page['lazyLoadCallback']) {
                add_action('load-' . $hook, $page['lazyLoadCallback']);
            }
        }
    }

    private function inlineLogoSvg(): string
    {
        $svg = file_get_contents(plugin_dir_path(\STAATIC_FILE) . 'assets/logo.svg');

        return sprintf('data:image/svg+xml;base64,%s', base64_encode($svg));
    }

    public function hidePagesFromMenu(?string $submenuFile): ?string
    {
        global $plugin_page;
        // Submenu slug with alternative item slug to highlight.
        $mapping = array_map(function ($page) {
            return $page['appearAs'];
        }, $this->pages);
        // Always remove the automatically generated submenu item for the parent slug
        $mapping[self::PARENT_SLUG] = null;
        // Select another submenu item to highlight.
        if ($plugin_page && isset($mapping[$plugin_page])) {
            $submenuFile = $mapping[$plugin_page];
        }
        // Hide the submenu.
        foreach (array_keys($mapping) as $submenu) {
            remove_submenu_page(self::PARENT_SLUG, $submenu);
        }

        return $submenuFile;
    }
}
