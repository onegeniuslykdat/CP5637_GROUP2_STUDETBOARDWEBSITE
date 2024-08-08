<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

use Staatic\WordPress\Request\TestRequest;
use Staatic\WordPress\Service\Scheduler;

final class ScheduleTestRequest implements ModuleInterface
{
    /**
     * @var Scheduler
     */
    private $scheduler;

    /** @var string */
    public const HOOK = 'staatic_test_request';

    /** @var string */
    public const SCHEDULE = 'staatic_maintenance_cron_interval';

    /**
     * @var TestRequest
     */
    public $testRequest;

    public function __construct(Scheduler $scheduler)
    {
        $this->scheduler = $scheduler;
    }

    public function hooks(): void
    {
        $this->testRequest = new TestRequest();
        add_action(self::HOOK, [$this->testRequest, 'dispatch']);
        add_action('wp_loaded', [$this, 'setupSchedule']);
    }

    public function setupSchedule(): void
    {
        if ($this->scheduler->isScheduled(self::HOOK) || !$this->scheduler->scheduleExists(self::SCHEDULE)) {
            return;
        }
        $this->testRequest->dispatch();
        $this->scheduler->schedule(self::HOOK, self::SCHEDULE);
    }
}
