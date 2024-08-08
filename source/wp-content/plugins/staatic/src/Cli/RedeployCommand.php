<?php

declare(strict_types=1);

namespace Staatic\WordPress\Cli;

use Staatic\Vendor\Psr\Log\LoggerInterface as PsrLoggerInterface;
use RuntimeException;
use Staatic\WordPress\Logging\LoggerInterface;
use Staatic\WordPress\Publication\PublicationManagerInterface;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Publication\PublicationTaskProvider;
use Staatic\WordPress\Service\Formatter;
use WP_CLI;
use function WP_CLI\Utils\get_flag_value;

class RedeployCommand
{
    /**
     * @var PsrLoggerInterface
     */
    protected $logger;

    /**
     * @var Formatter
     */
    protected $formatter;

    /**
     * @var PublicationRepository
     */
    protected $publicationRepository;

    /**
     * @var PublicationManagerInterface
     */
    protected $publicationManager;

    /**
     * @var PublicationTaskProvider
     */
    protected $taskProvider;

    use PublishesFromCli;

    /**
     * @param mixed $logger
     */
    public function __construct($logger, Formatter $formatter, PublicationRepository $publicationRepository, PublicationManagerInterface $publicationManager, PublicationTaskProvider $taskProvider)
    {
        $this->logger = $logger;
        $this->formatter = $formatter;
        $this->publicationRepository = $publicationRepository;
        $this->publicationManager = $publicationManager;
        $this->taskProvider = $taskProvider;
    }

    /**
     * Redeploys an existing publication using the active deployment method.
     *
     * ## OPTIONS
     *
     * <id>
     * : Id of the publication.
     *
     * [--[no-]force]
     * : Whether or not to force publishing, even if another publication is in progress.
     * ---
     * default: false
     *
     * [--[no-]verbose]
     * : Whether or not to output logs during publication.
     * ---
     * default: false
     * ---
     *
     * ## EXAMPLES
     *
     *     wp staatic redeploy abc-def-123
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args): void
    {
        [$publicationId] = $args;
        $verbose = get_flag_value($assoc_args, 'verbose', \false);
        $force = get_flag_value($assoc_args, 'force', \false);
        if ($verbose && $this->logger instanceof LoggerInterface) {
            $this->logger->enableConsoleLogger();
        }
        if ($this->publicationManager->isPublicationInProgress()) {
            if ($force) {
                $publication = $this->publicationRepository->find(get_option('staatic_current_publication_id'));
                $this->publicationManager->cancelPublication($publication);
                update_option('staatic_current_publication_id', null);
            } else {
                WP_CLI::error(__('Unable to publish; another publication is pending', 'staatic'));
            }
        }
        if (!$publication = $this->publicationRepository->find($publicationId)) {
            WP_CLI::error(sprintf(
                /* translators: 1: Publication identifier. */
                __('Unable to find source publication "%1$s"', 'staatic'),
                $publicationId
            ));
        }
        if (!$publication->build()->isFinishedCrawling()) {
            WP_CLI::error(sprintf(
                /* translators: 1: Publication identifier. */
                __('Source publication "%1$s" has not finished crawling', 'staatic'),
                $publicationId
            ));
        }
        $publication = $this->publicationManager->createPublication([
            'sourcePublicationId' => $publicationId
        ], $publication->build(), null, $publication->isPreview());
        if ($this->publicationManager->claimPublication($publication)) {
            $this->startPublication($publication);
        } else {
            $this->publicationManager->cancelPublication($publication);

            throw new RuntimeException(__('Unable to claim publication; another publication is pending', 'staatic'));
        }
    }
}
