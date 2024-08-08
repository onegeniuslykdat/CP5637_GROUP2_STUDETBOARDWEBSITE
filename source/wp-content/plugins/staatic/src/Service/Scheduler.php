<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use RuntimeException;

final class Scheduler
{
    public function scheduleExists(string $schedule): bool
    {
        $schedules = $this->getSchedules();

        return isset($schedules[$schedule]);
    }

    public function getSchedules(): array
    {
        return wp_get_schedules();
    }

    public function isScheduled(string $event): bool
    {
        //!COMPAT: Introduced in WordPress 5.1.0 - replace with something else...
        // https://developer.wordpress.org/reference/functions/wp_get_scheduled_event/
        $nextEvent = Polyfill::wp_get_scheduled_event($event);

        return $nextEvent !== \false;
    }

    public function unschedule(string $event): void
    {
        $nextEventTimestamp = wp_next_scheduled($event);
        if (!is_int($nextEventTimestamp)) {
            throw new RuntimeException("Unable to unschedule event '{$event}'; event does not exist");
        }
        $result = Polyfill::wp_unschedule_event($nextEventTimestamp, $event);
        if ($result !== \true) {
            throw new RuntimeException(
                "Unable to unschedule event '{$event}' scheduled at timestamp '{$nextEventTimestamp}'; unknown error"
            );
        }
    }

    public function schedule(string $event, string $schedule): bool
    {
        return Polyfill::wp_schedule_event(time(), $schedule, $event, []);
    }
}
