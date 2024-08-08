<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication;

use DateTimeImmutable;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Framework\Build;
use Staatic\Framework\BuildRepository\BuildRepositoryInterface;
use Staatic\Framework\Deployment;
use Staatic\WordPress\Factory\DeploymentFactory;
use Staatic\WordPress\Service\AdditionalPaths;
use Staatic\WordPress\Service\AdditionalUrls;
use Staatic\WordPress\Service\SiteUrlProvider;
use Staatic\WordPress\Setting\Build\DestinationUrlSetting;
use Staatic\WordPress\Setting\Build\PreviewUrlSetting;
use WP_Error;
use wpdb;

final class PublicationManager implements PublicationManagerInterface
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @var BuildRepositoryInterface
     */
    private $buildRepository;

    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var BackgroundPublisher
     */
    private $backgroundPublisher;

    /**
     * @var DeploymentFactory
     */
    private $deploymentFactory;

    /**
     * @var DestinationUrlSetting
     */
    private $destinationUrl;

    /**
     * @var PreviewUrlSetting
     */
    private $previewUrl;

    /**
     * @var SiteUrlProvider
     */
    private $siteUrlProvider;

    public function __construct(wpdb $wpdb, BuildRepositoryInterface $buildRepository, PublicationRepository $publicationRepository, BackgroundPublisher $backgroundPublisher, DeploymentFactory $deploymentFactory, DestinationUrlSetting $destinationUrl, PreviewUrlSetting $previewUrl, SiteUrlProvider $siteUrlProvider)
    {
        $this->wpdb = $wpdb;
        $this->buildRepository = $buildRepository;
        $this->publicationRepository = $publicationRepository;
        $this->backgroundPublisher = $backgroundPublisher;
        $this->deploymentFactory = $deploymentFactory;
        $this->destinationUrl = $destinationUrl;
        $this->previewUrl = $previewUrl;
        $this->siteUrlProvider = $siteUrlProvider;
    }

    public function currentPublicationId(): ?string
    {
        // Cannot use get_option() since this may be cached and we do not want that.
        $currentPublicationId = $this->wpdb->get_var(
            $this->wpdb->prepare("SELECT option_value FROM {$this->wpdb->prefix}options WHERE option_name = %s", 'staatic_current_publication_id')
        );

        return $currentPublicationId ?: null;
    }

    public function isPublicationInProgress(): bool
    {
        return (bool) $this->currentPublicationId();
    }

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
    ): Publication
    {
        $build = $build ?? $this->createBuild($isPreview);
        $deployment = $deployment ?? $this->deploymentFactory->create($build->id());
        $publication = new Publication(
            $this->publicationRepository->nextId(),
            new DateTimeImmutable(),
            $build,
            $deployment,
            $isPreview,
            get_current_user_id() ?: null,
            $metadata
        );
        $this->publicationRepository->add($publication);

        return $publication;
    }

    /**
     * @param Publication $publication
     */
    public function claimPublication($publication): bool
    {
        if ($this->isPublicationInProgress()) {
            return \false;
        }
        update_option('staatic_current_publication_id', $publication->id());
        update_option('staatic_latest_publication_id', $publication->id());

        return \true;
    }

    /**
     * @param Publication $publication
     */
    public function cancelPublication($publication): void
    {
        $publication->markCanceled();
        $this->publicationRepository->update($publication);
    }

    /**
     * @param Publication $publication
     */
    public function initiateBackgroundPublisher($publication): void
    {
        $this->backgroundPublisher->initiatePublication($publication);
    }

    /**
     * @param Publication $publication
     */
    public function cancelBackgroundPublisher($publication): void
    {
        $this->backgroundPublisher->cancelPublication($publication);
    }

    /**
     * @param bool $isPreview
     * @param string|null $parentBuildId
     */
    public function createBuild($isPreview = \false, $parentBuildId = null): Build
    {
        $destinationUrl = $isPreview ? $this->previewUrl->value() : $this->destinationUrl->value();
        $build = new Build($this->buildRepository->nextId(), ($this->siteUrlProvider)(), new Uri(
            $destinationUrl
        ), $parentBuildId);
        $this->buildRepository->add($build);

        return $build;
    }

    /**
     * @param string $urls
     * @param string $paths
     */
    public function validateSubsetRequest($urls, $paths): WP_Error
    {
        $errors = new WP_Error();
        if (!$urls && !$paths) {
            $errors->add('form', __('Please add one or more URLs or paths.', 'staatic'));

            return $errors;
        }
        if ($urls) {
            $urlErrors = AdditionalUrls::validate($urls, ($this->siteUrlProvider)());
            $errors->merge_from($urlErrors);
        }
        if ($paths) {
            $pathErrors = AdditionalPaths::validate($paths);
            $errors->merge_from($pathErrors);
        }

        return $errors;
    }
}
