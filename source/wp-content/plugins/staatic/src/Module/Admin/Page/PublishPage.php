<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page;

use Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Publication\PublicationManager;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Service\PartialRenderer;

final class PublishPage implements ModuleInterface
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
     * @var PublicationManager
     */
    private $publicationManager;

    use FlashesMessages, TriggersPublications;

    /** @var string */
    public const PAGE_SLUG = 'staatic-publish';

    public function __construct(AdminNavigation $navigation, PartialRenderer $renderer, PublicationRepository $publicationRepository, PublicationManager $publicationManager)
    {
        $this->navigation = $navigation;
        $this->renderer = $renderer;
        $this->publicationRepository = $publicationRepository;
        $this->publicationManager = $publicationManager;
    }

    public function hooks(): void
    {
        if (!is_admin()) {
            return;
        }
        add_action('init', [$this, 'addMenuItem']);
    }

    public function addMenuItem(): void
    {
        $this->navigation->addPage(
            __('Publish', 'staatic'),
            self::PAGE_SLUG,
            [$this, 'render'],
            'staatic_publish',
            PublicationsPage::PAGE_SLUG
        );
    }

    public function render(): void
    {
        if (!empty($_REQUEST['cancel'])) {
            check_admin_referer('staatic-publish_cancel');
            $publicationId = sanitize_key($_REQUEST['cancel']);
            if (!$publication = $this->publicationRepository->find($publicationId)) {
                update_option('staatic_current_publication_id', null);
                wp_die(sprintf(
                    /* translators: %s: Publication ID. */
                    __('Publication (#%s) not found', 'staatic'),
                    $publicationId
                ));
            }
            //!TODO: what if ran from CLI command?
            $this->publicationManager->cancelBackgroundPublisher($publication);
            $this->renderFlashMessage(
                __('Publish', 'staatic'),
                __('Publication cancellation is in progress and will be completed soon.', 'staatic')
            );

            return;
        }
        check_admin_referer('staatic-publish');
        $this->triggerPublication(__('Publish', 'staatic'), function () {
            return $this->createPublication();
        });
    }

    private function createPublication(): Publication
    {
        if (isset($_REQUEST['redeploy'])) {
            if (!$publicationId = sanitize_key($_REQUEST['redeploy'])) {
                wp_die(__('Missing source publication ID.', 'staatic'));
            }
            if (!$publication = $this->publicationRepository->find($publicationId)) {
                wp_die(__('Invalid source publication.', 'staatic'));
            }
            if (!$publication->build()->isFinishedCrawling()) {
                wp_die(__('Invalid source publication.', 'staatic'));
            }

            return $this->publicationManager->createPublication([
                'sourcePublicationId' => $publicationId
            ], $publication->build(), null, $publication->isPreview());
        }

        return $this->publicationManager->createPublication([], null, null, !empty($_REQUEST['preview']));
    }
}
