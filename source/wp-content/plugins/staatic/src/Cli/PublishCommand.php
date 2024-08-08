<?php

declare(strict_types=1);

namespace Staatic\WordPress\Cli;

use Staatic\Vendor\Psr\Log\LoggerInterface as PsrLoggerInterface;
use RuntimeException;
use Staatic\WordPress\Logging\LoggerInterface;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Publication\PublicationManagerInterface;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Publication\PublicationTaskProvider;
use Staatic\WordPress\Service\Formatter;
use WP_CLI;
use function WP_CLI\Utils\get_flag_value;

class PublishCommand
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
     * @var bool
     */
    private $preview;

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
     * Initiates publication process to create and deploy a static WordPress site.
     *
     * ## OPTIONS
     *
     * [--only-urls=<urls>]
     * : Optionally a comma-separated list of URLs to update when publishing selectively.
     *
     * [--only-paths=<paths>]
     * : Optionally a comma-separated list of filesystem paths to update when publishing selectively.
     *
     * [--[no-]deploy]
     * : Whether or not to deploy the publication using the configured deployment method.
     * ---
     * default: true
     *
     * [--[no-]preview]
     * : Whether or not to create a preview build, if supported by the deployment method.
     * ---
     * default: false
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
     *     wp staatic publish
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args): void
    {
        $this->preview = get_flag_value($assoc_args, 'preview', \false);
        $deploy = get_flag_value($assoc_args, 'deploy', \true);
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
        $urls = implode("\n", array_filter(array_map(function ($value) {
            return trim($value);
        }, explode(',', get_flag_value($assoc_args, 'only-urls', '')))));
        $paths = implode("\n", array_filter(array_map(function ($value) {
            return trim($value);
        }, explode(',', get_flag_value($assoc_args, 'only-paths', '')))));
        $this->validateRequest($urls, $paths);
        $publication = $this->createPublication($urls, $paths, $deploy);
        if ($this->publicationManager->claimPublication($publication)) {
            $this->startPublication($publication);
        } else {
            $this->publicationManager->cancelPublication($publication);

            throw new RuntimeException(__('Unable to claim publication; another publication is pending', 'staatic'));
        }
    }

    /**
     * @param string $urls
     * @param string $paths
     */
    protected function validateRequest($urls, $paths): void
    {
        if (!$urls && !$paths) {
            return;
        }
        $errors = $this->publicationManager->validateSubsetRequest($urls, $paths);
        if ($errors->has_errors()) {
            WP_CLI::error($errors);
        }
    }

    /**
     * @param string $urls
     * @param string $paths
     * @param bool $deploy
     */
    protected function createPublication($urls, $paths, $deploy): Publication
    {
        $metadata = [];
        if ($urls || $paths) {
            $metadata['subset'] = [
                'urls' => $urls,
                'paths' => $paths
            ];
        }
        if (!$deploy) {
            $metadata['skipDeploy'] = \true;
        }

        return $this->publicationManager->createPublication($metadata, null, null, $this->preview);
    }
}
