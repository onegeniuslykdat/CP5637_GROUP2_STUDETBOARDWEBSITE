<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication\Task;

use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Publication\PublicationRepository;

final class FinishTask implements TaskInterface
{
    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    public function __construct(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    public static function name(): string
    {
        return 'finish';
    }

    public function description(): string
    {
        return __('Finishing', 'staatic');
    }

    /**
     * @param Publication $publication
     */
    public function supports($publication): bool
    {
        return \true;
    }

    /**
     * @param Publication $publication
     * @param bool $limitedResources
     */
    public function execute($publication, $limitedResources): bool
    {
        $publication->markFinished();
        $this->publicationRepository->update($publication);
        if ($publication->deployment()->dateFinished()) {
            $activePublicationOption = $publication->isPreview() ? 'staatic_active_preview_publication_id' : 'staatic_active_publication_id';
            update_option($activePublicationOption, $publication->id());
        }
        update_option('staatic_current_publication_id', '');

        return \true;
    }
}
