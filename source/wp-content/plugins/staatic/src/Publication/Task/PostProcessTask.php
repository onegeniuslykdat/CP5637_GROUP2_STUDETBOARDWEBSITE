<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication\Task;

use Staatic\WordPress\Factory\StaticGeneratorFactory;
use Staatic\WordPress\Publication\Publication;

final class PostProcessTask implements TaskInterface
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
        return 'post_process';
    }

    public function description(): string
    {
        return __('Post-processing', 'staatic');
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
        $staticGenerator->postProcess($publication->build());

        return \true;
    }
}
