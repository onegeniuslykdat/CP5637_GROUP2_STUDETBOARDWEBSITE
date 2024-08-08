<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Promise;

interface PromiseInterface
{
    public const PENDING = 'pending';
    public const FULFILLED = 'fulfilled';
    public const REJECTED = 'rejected';
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     */
    public function then($onFulfilled = null, $onRejected = null): PromiseInterface;
    /**
     * @param callable $onRejected
     */
    public function otherwise($onRejected): PromiseInterface;
    public function getState(): string;
    public function resolve($value): void;
    public function reject($reason): void;
    public function cancel(): void;
    /**
     * @param bool $unwrap
     */
    public function wait($unwrap = \true);
}
