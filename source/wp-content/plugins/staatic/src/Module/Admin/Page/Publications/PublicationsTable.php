<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\Publications;

use Staatic\WordPress\ListTable\AbstractListTable;
use Staatic\WordPress\ListTable\BulkAction\BulkAction;
use Staatic\WordPress\ListTable\Column\ColumnFactory;
use Staatic\WordPress\ListTable\Column\UserColumn;
use Staatic\WordPress\ListTable\RowAction\RowAction;
use Staatic\WordPress\ListTable\View\View;
use Staatic\WordPress\Module\Admin\Page\PublishPage;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Publication\PublicationStatus;
use Staatic\WordPress\Publication\PublicationTaskProvider;
use Staatic\WordPress\Service\Formatter;

class PublicationsTable extends AbstractListTable
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var ColumnFactory
     */
    private $columnFactory;

    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var PublicationTaskProvider
     */
    private $publicationTaskProvider;

    /** @var string */
    protected const NAME = 'publication_list_table';

    public function __construct(Formatter $formatter, ColumnFactory $columnFactory, PublicationRepository $publicationRepository, PublicationTaskProvider $publicationTaskProvider)
    {
        $this->formatter = $formatter;
        $this->columnFactory = $columnFactory;
        $this->publicationRepository = $publicationRepository;
        $this->publicationTaskProvider = $publicationTaskProvider;
        parent::__construct('id', ['date_created', 'DESC']);
    }

    /**
     * @param string $baseUrl
     * @param mixed[] $arguments
     */
    public function initialize($baseUrl, $arguments = []): void
    {
        parent::initialize($baseUrl, $arguments);
        $this->setupColumns();
        $this->setupViews();
        $this->setupRowActions();
        $this->setupBulkActions();
    }

    public function setupColumns(): void
    {
        $this->addColumns([new PublicationTitleColumn($this->formatter, 'date_created', __('Publication', 'staatic'), [
            'activePublicationId' => $this->activePublicationId()
        ]), $this->columnFactory->create(UserColumn::class, [
            'name' => 'user_id',
            'label' => __('Publisher', 'staatic')
        ]), new PublicationStatusColumn(
            $this->formatter,
            $this->publicationTaskProvider,
            'status',
            __('Status', 'staatic')
        )]);
    }

    public function setupViews(): void
    {
        $publicationTypes = PublicationStatus::labels();
        foreach ($publicationTypes as $name => $label) {
            $this->addView(new View($name, $label));
        }
    }

    public function setupRowActions(): void
    {
        $this->addRowActions([
            new RowAction('details', __('Details', 'staatic'), function ($itemId) {
                        return admin_url(
                            sprintf('admin.php?page=%s&id=%s', PublicationSummaryPage::PAGE_SLUG, $itemId)
                        );
                    }), new RowAction('download', __('Download', 'staatic'), function ($itemId) {
                        return admin_url(
                            sprintf('admin.php?page=%s&id=%s', PublicationDownloadPage::PAGE_SLUG, $itemId)
                        );
                    }), new RowAction('redeploy', __('(Re)deploy', 'staatic'), function ($itemId) {
                        return wp_nonce_url(
                            admin_url(sprintf('admin.php?page=%s&redeploy=%s', PublishPage::PAGE_SLUG, $itemId)),
                            "staatic-publish"
                        );
                    }, function ($item) {
                        return $this->canRedeployPublication($item);
                    }, [
                        'class' => 'submitredeploy'
                    ]), new RowAction('delete', __('Delete', 'staatic'), function ($itemId) {
                                return wp_nonce_url(
                                    admin_url(
                                        sprintf('admin.php?page=%s&id=%s', PublicationDeletePage::PAGE_SLUG, $itemId)
                                    ),
                                    "staatic-publication-delete_{$itemId}"
                                );
                            }, function ($item) {
                                return $this->canDeletePublication($item);
                            }, [
                                'class' => 'submitdelete'
                            ])]);
    }

    public function setupBulkActions(): void
    {
        $this->addBulkAction(new BulkAction('delete', __('Delete', 'staatic'), [$this, 'bulkDelete']));
    }

    /**
     * @param mixed[] $itemIds
     */
    public function bulkDelete($itemIds): void
    {
        $numItemsDeleted = 0;
        foreach ($itemIds as $itemId) {
            if (!$item = $this->publicationRepository->find($itemId)) {
                continue;
            }
            if (!$this->canDeletePublication($item)) {
                continue;
            }
            $this->publicationRepository->delete($item);
            $numItemsDeleted++;
        }
        $redirectTo = remove_query_arg(['deleted', 'item'], wp_get_referer());
        $redirectTo = add_query_arg('paged', $this->wpListTable()->get_pagenum(), $redirectTo);
        $redirectTo = add_query_arg('deleted', $numItemsDeleted, $redirectTo);
        wp_safe_redirect($redirectTo);
        exit;
    }

    private function activePublicationId(): ?string
    {
        return get_option('staatic_active_publication_id', null);
    }

    private function canRedeployPublication(Publication $publication): bool
    {
        return $publication->build()->isFinishedCrawling();
    }

    private function canDeletePublication(Publication $publication): bool
    {
        return !in_array(
            $publication->id(),
            [
                get_option('staatic_current_publication_id', null),
                get_option('staatic_latest_publication_id', null),
                get_option('staatic_active_publication_id', null),
                get_option('staatic_active_preview_publication_id', null)
            ],
            \true
        );
    }

    /**
     * @param string|null $view
     * @param string|null $query
     * @param int $limit
     * @param int $offset
     * @param string|null $orderBy
     * @param string|null $direction
     */
    public function items($view, $query, $limit, $offset, $orderBy, $direction): array
    {
        return $this->publicationRepository->findWhereMatching($view, $query, $limit, $offset, $orderBy, $direction);
    }

    /**
     * @param string|null $view
     * @param string|null $query
     */
    public function numItems($view, $query): int
    {
        return $this->publicationRepository->countWhereMatching($view, $query);
    }

    public function numItemsPerView(): ?array
    {
        return $this->publicationRepository->getPublicationsPerStatus();
    }
}
