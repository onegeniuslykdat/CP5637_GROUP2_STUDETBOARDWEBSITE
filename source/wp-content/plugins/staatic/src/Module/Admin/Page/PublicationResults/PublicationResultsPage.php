<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\PublicationResults;

use Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Service\PartialRenderer;

final class PublicationResultsPage implements ModuleInterface
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
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var PublicationResultsTable
     */
    private $listTable;

    /** @var string */
    public const PAGE_SLUG = 'staatic-publication-results';

    /**
     * @var Publication|null
     */
    private $publication;

    public function __construct(AdminNavigation $navigation, PartialRenderer $renderer, PublicationRepository $publicationRepository, PublicationResultsTable $listTable)
    {
        $this->navigation = $navigation;
        $this->renderer = $renderer;
        $this->publicationRepository = $publicationRepository;
        $this->listTable = $listTable;
    }

    public function hooks(): void
    {
        if (!is_admin()) {
            return;
        }
        add_action('init', [$this, 'addPage']);
        $this->listTable->registerHooks(sprintf('staatic_page_%s', self::PAGE_SLUG));
    }

    public function addPage(): void
    {
        $this->navigation->addPage(
            __('Publication Resources', 'staatic'),
            self::PAGE_SLUG,
            [$this, 'render'],
            'staatic_publish',
            PublicationsPage::PAGE_SLUG,
            [$this, 'load']
        );
    }

    public function load(): void
    {
        $publicationId = isset($_REQUEST['id']) ? sanitize_key($_REQUEST['id']) : null;
        if (!$publicationId) {
            wp_die(__('Missing publication id.', 'staatic'));
        }
        if (!$this->publication = $this->publicationRepository->find($publicationId)) {
            wp_die(__('Invalid publication.', 'staatic'));
        }
        $this->listTable->initialize(
            admin_url(sprintf('admin.php?page=%s&id=%s', self::PAGE_SLUG, $this->publication->id())),
            [
            'buildId' => $this->publication->build()->id()
        ]
        );
    }

    public function render(): void
    {
        $listTable = $this->listTable->wpListTable();
        $listTable->prepare_items();
        $this->renderer->render('admin/publication/results.php', [
            'listTable' => $listTable,
            'publication' => $this->publication
        ]);
    }
}
