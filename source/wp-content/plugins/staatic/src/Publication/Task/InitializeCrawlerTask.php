<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication\Task;

use RuntimeException;
use Staatic\WordPress\Factory\StaticGeneratorFactory;
use Staatic\WordPress\Publication\Publication;

final class InitializeCrawlerTask implements TaskInterface
{
    /**
     * @var StaticGeneratorFactory
     */
    private $factory;

    public function __construct(StaticGeneratorFactory $factory)
    {
        $this->factory = $factory;
    }

    public static function name(): string
    {
        return 'initialize_crawler';
    }

    public function description(): string
    {
        return __('Initializing crawler', 'staatic');
    }

    /**
     * @param Publication $publication
     */
    public function supports($publication): bool
    {
        if ($publication->metadataByKey('sourcePublicationId')) {
            // Skip in case an existing publication was this publication's source.
            return \false;
        }

        return \true;
    }

    /**
     * @param Publication $publication
     * @param bool $limitedResources
     */
    public function execute($publication, $limitedResources): bool
    {
        $staticGenerator = ($this->factory)($publication, $limitedResources);
        $crawlUrlProviders = $this->factory->createCrawlUrlProviders();
        $numEnqueued = $staticGenerator->initializeCrawler($publication->build(), $crawlUrlProviders);
        if (!$numEnqueued) {
            throw new RuntimeException('No crawl urls were enqueued; nothing to do!');
        }

        return \true;
    }
}
