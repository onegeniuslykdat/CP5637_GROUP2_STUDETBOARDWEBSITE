<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

use Staatic\WordPress\Logging\LogEntryCleanup;
use Staatic\WordPress\Publication\PublicationCleanup;
use Staatic\WordPress\Service\ResourceCleanup;
use Staatic\WordPress\Service\Scheduler;

final class Cleanup implements ModuleInterface
{
    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var LogEntryCleanup
     */
    private $logEntryCleanup;

    /**
     * @var PublicationCleanup
     */
    private $publicationCleanup;

    /**
     * @var ResourceCleanup
     */
    private $resourceCleanup;

    /** @var string */
    public const HOOK = 'staatic_cleanup';

    /** @var string */
    public const SCHEDULE = 'staatic_maintenance_cron_interval';

    public function __construct(Scheduler $scheduler, LogEntryCleanup $logEntryCleanup, PublicationCleanup $publicationCleanup, ResourceCleanup $resourceCleanup)
    {
        $this->scheduler = $scheduler;
        $this->logEntryCleanup = $logEntryCleanup;
        $this->publicationCleanup = $publicationCleanup;
        $this->resourceCleanup = $resourceCleanup;
    }

    public function hooks(): void
    {
        add_action(self::HOOK, [$this, 'cleanup']);
        add_action('wp_loaded', [$this, 'setupSchedule']);
    }

    public function setupSchedule(): void
    {
        if ($this->scheduler->isScheduled(self::HOOK) || !$this->scheduler->scheduleExists(self::SCHEDULE)) {
            return;
        }
        $this->scheduler->schedule(self::HOOK, self::SCHEDULE);
    }

    public function cleanup(): void
    {
        $this->logEntryCleanup->cleanup();
        $this->publicationCleanup->cleanup();
        $this->resourceCleanup->cleanup();
    }
}
