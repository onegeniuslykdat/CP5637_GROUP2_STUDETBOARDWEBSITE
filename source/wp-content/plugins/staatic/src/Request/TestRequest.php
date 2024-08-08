<?php

declare(strict_types=1);

namespace Staatic\WordPress\Request;

use Staatic\Vendor\WP_Async_Request;

final class TestRequest extends WP_Async_Request
{
    /** @var string */
    public const OPTION_NAME = 'staatic_test_request_status';

    /** @var int */
    public const TIME_LIMIT = 1800;

    /** @var int */
    public const SLEEP = 5;

    /** @var string */
    protected $prefix = 'staatic';

    /** @var string */
    protected $action = 'test_request';

    protected function handle()
    {
        $testRequestEnabled = apply_filters('staatic_test_request_enabled', \true);
        if (!$testRequestEnabled) {
            return;
        }
        $value = (string) get_option(self::OPTION_NAME);
        $start = time();
        if ($value) {
            [$prevStart] = explode('_', $value);
            if ($prevStart && $start - $prevStart < self::TIME_LIMIT) {
                // Conflict; may still be running.
                return;
            }
        }
        update_option(self::OPTION_NAME, (string) $start);
        do {
            sleep(self::SLEEP);
            $now = time();
            update_option(self::OPTION_NAME, "{$start}_{$now}");
        } while ($now - $start < self::TIME_LIMIT);
        update_option(self::OPTION_NAME, "{$start}_{$now}_done");
    }
}
