<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\Publications;

use Exception;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Service\PublicationArchiver;

final class PublicationDownloadPage implements ModuleInterface
{
    /**
     * @var AdminNavigation
     */
    private $navigation;

    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var PublicationArchiver
     */
    private $publicationArchiver;

    /** @var string */
    public const PAGE_SLUG = 'staatic-publication-download';

    public function __construct(AdminNavigation $navigation, PublicationRepository $publicationRepository, PublicationArchiver $publicationArchiver)
    {
        $this->navigation = $navigation;
        $this->publicationRepository = $publicationRepository;
        $this->publicationArchiver = $publicationArchiver;
    }

    public function hooks(): void
    {
        if (!is_admin()) {
            return;
        }
        add_action('init', [$this, 'addPage']);
    }

    public function addPage(): void
    {
        $this->navigation->addPage(
            __('Download Publication', 'staatic'),
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
        if (!$this->publicationRepository->find($publicationId)) {
            wp_die(__('Invalid publication.', 'staatic'));
        }

        try {
            ($this->publicationArchiver)($publicationId);
        } catch (Exception $e) {
            wp_die(sprintf(
                /* translators: %s: Error Message. */
                __('Unable to generate archive: %s.', 'staatic'),
                $e->getMessage()
            ));
        }
    }

    public function render(): void
    {
    }
}
