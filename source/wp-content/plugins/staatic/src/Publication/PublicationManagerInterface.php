<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication;

use Staatic\Framework\Build;
use Staatic\Framework\Deployment;
use WP_Error;

interface PublicationManagerInterface
{
    public function isPublicationInProgress(): bool;

    /**
     * @param mixed[] $metadata
     * @param Build|null $build
     * @param Deployment|null $deployment
     * @param bool $isPreview
     */
    public function createPublication(
        $metadata = [],
        $build = null,
        $deployment = null,
        $isPreview = \false
    ): Publication;

    /**
     * @param Publication $publication
     */
    public function claimPublication($publication): bool;

    /**
     * @param Publication $publication
     */
    public function cancelPublication($publication): void;

    /**
     * @param Publication $publication
     */
    public function initiateBackgroundPublisher($publication): void;

    /**
     * @param Publication $publication
     */
    public function cancelBackgroundPublisher($publication): void;

    /**
     * @param string $urls
     * @param string $paths
     */
    public function validateSubsetRequest($urls, $paths): WP_Error;
}
