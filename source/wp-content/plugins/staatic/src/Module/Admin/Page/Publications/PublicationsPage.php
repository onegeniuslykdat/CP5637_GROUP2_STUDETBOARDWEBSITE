<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\Publications;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Service\PartialRenderer;

final class PublicationsPage implements ModuleInterface
{
    /**
     * @var AdminNavigation
     */
    private $navigation;

    /**
     * @var PartialRenderer
     */
    private $renderer;

    /**
     * @var PublicationsTable
     */
    private $listTable;

    /** @var string */
    public const PAGE_SLUG = 'staatic-publications';

    public function __construct(AdminNavigation $navigation, PartialRenderer $renderer, PublicationsTable $listTable)
    {
        $this->navigation = $navigation;
        $this->renderer = $renderer;
        $this->listTable = $listTable;
    }

    public function hooks(): void
    {
        if (!is_admin()) {
            return;
        }
        add_action('init', [$this, 'addMenuItem']);
        $this->listTable->registerHooks(sprintf('staatic_page_%s', self::PAGE_SLUG));
    }

    public function addMenuItem(): void
    {
        $this->navigation->addMenuItem(
            __('Publications', 'staatic'),
            __('Latest Publications', 'staatic'),
            self::PAGE_SLUG,
            [$this, 'render'],
            'staatic_publish',
            [$this, 'load'],
            10
        );
    }

    public function load(): void
    {
        // Display notices.
        $deleted = isset($_REQUEST['deleted']) ? (int) $_REQUEST['deleted'] : 0;
        $messages = [];
        if ($deleted > 0) {
            /* translators: %s: Number of publications. */
            $messages[] = sprintf(
                _n('%s publication deleted.', '%s publications deleted.', $deleted),
                number_format_i18n($deleted)
            );
        }
        if (count($messages) > 0) {
            add_action('admin_notices', function () use ($messages) {
                printf('<div class="updated notice is-dismissible"><p>%s</p></div>', implode("<br>\n", $messages));
            });
        }
        // Setup list table.
        $this->listTable->initialize(admin_url(sprintf('admin.php?page=%s', self::PAGE_SLUG)));
        $this->listTable->processBulkActions();
    }

    public function render(): void
    {
        $listTable = $this->listTable->wpListTable();
        $listTable->prepare_items();
        $this->renderer->render('admin/publication/list.php', compact('listTable'));
    }
}
