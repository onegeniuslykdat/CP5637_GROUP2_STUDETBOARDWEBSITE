<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication\Task;

use Staatic\WordPress\Publication\Publication;

interface TaskInterface
{
    public static function name(): string;

    public function description(): string;

    /**
     * @param Publication $publication
     */
    public function supports($publication): bool;

    /**
     * @param Publication $publication
     * @param bool $limitedResources
     */
    public function execute($publication, $limitedResources): bool;
}
