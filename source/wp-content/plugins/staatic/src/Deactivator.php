<?php

declare(strict_types=1);

namespace Staatic\WordPress;

use Staatic\WordPress\Module\Cleanup;
use Staatic\WordPress\Module\ScheduleTestRequest;
use Staatic\WordPress\Service\Scheduler;

final class Deactivator
{
    /**
     * @var Scheduler
     */
    private $scheduler;

    public function __construct(Scheduler $scheduler)
    {
        $this->scheduler = $scheduler;
    }

    public function deactivate(): void
    {
        $this->unscheduleEvents();
    }

    private function unscheduleEvents(): void
    {
        if ($this->scheduler->isScheduled(Cleanup::HOOK)) {
            $this->scheduler->unschedule(Cleanup::HOOK);
        }
        if ($this->scheduler->isScheduled(ScheduleTestRequest::HOOK)) {
            $this->scheduler->unschedule(ScheduleTestRequest::HOOK);
        }
    }
}
