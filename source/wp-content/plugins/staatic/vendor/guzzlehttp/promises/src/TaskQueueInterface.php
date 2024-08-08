<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Promise;

interface TaskQueueInterface
{
    public function isEmpty(): bool;
    /**
     * @param callable $task
     */
    public function add($task): void;
    public function run(): void;
}
