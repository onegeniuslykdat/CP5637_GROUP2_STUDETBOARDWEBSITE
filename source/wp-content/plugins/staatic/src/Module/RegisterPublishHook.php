<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

use Staatic\WordPress\Publication\PublicationManager;

final class RegisterPublishHook implements ModuleInterface
{
    /**
     * @var PublicationManager
     */
    private $publicationManager;

    /** @var string */
    public const HOOK = 'staatic_publish';

    public function __construct(PublicationManager $publicationManager)
    {
        $this->publicationManager = $publicationManager;
    }

    public function hooks(): void
    {
        add_action(self::HOOK, [$this, 'publish']);
    }

    public function publish(): void
    {
        if ($this->publicationManager->isPublicationInProgress()) {
            return;
        }
        $publication = $this->publicationManager->createPublication();
        if ($this->publicationManager->claimPublication($publication)) {
            $this->publicationManager->initiateBackgroundPublisher($publication);
        } else {
            $this->publicationManager->cancelPublication($publication);
        }
    }
}
