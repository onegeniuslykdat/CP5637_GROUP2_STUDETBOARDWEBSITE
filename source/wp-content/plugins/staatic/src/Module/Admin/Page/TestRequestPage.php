<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Request\TestRequest;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Service\PartialRenderer;

final class TestRequestPage implements ModuleInterface
{
    /**
     * @var AdminNavigation
     */
    private $navigation;

    /**
     * @var PartialRenderer
     */
    private $renderer;

    use FlashesMessages;

    /** @var string */
    public const PAGE_SLUG = 'staatic-test-request';

    /**
     * @var TestRequest
     */
    private $request;

    public function __construct(AdminNavigation $navigation, PartialRenderer $renderer)
    {
        $this->navigation = $navigation;
        $this->renderer = $renderer;
    }

    public function hooks(): void
    {
        $this->request = new TestRequest();
        if (!is_admin()) {
            return;
        }
        add_action('init', [$this, 'addPage']);
    }

    public function addPage(): void
    {
        $this->navigation->addPage(
            __('Publication Test Task', 'staatic'),
            self::PAGE_SLUG,
            [$this, 'render'],
            'staatic_manage_settings',
            SettingsPage::PAGE_SLUG,
            [$this, 'load']
        );
    }

    public function load(): void
    {
        $this->request->dispatch();
    }

    public function render(): void
    {
        $this->renderFlashMessage(
            __('Publication Test Task', 'staatic'),
            __('Publication test task has been dispatched; this will take a couple of minutes to complete.', 'staatic'),
            admin_url('site-health.php')
        );
    }
}
