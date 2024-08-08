<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Promise;

use Generator;
use Throwable;
final class Coroutine implements PromiseInterface
{
    private $currentPromise;
    private $generator;
    private $result;
    public function __construct(callable $generatorFn)
    {
        $this->generator = $generatorFn();
        $this->result = new Promise(function (): void {
            while (isset($this->currentPromise)) {
                $this->currentPromise->wait();
            }
        });
        try {
            $this->nextCoroutine($this->generator->current());
        } catch (Throwable $throwable) {
            $this->result->reject($throwable);
        }
    }
    /**
     * @param callable $generatorFn
     */
    public static function of($generatorFn): self
    {
        return new self($generatorFn);
    }
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     */
    public function then($onFulfilled = null, $onRejected = null): PromiseInterface
    {
        return $this->result->then($onFulfilled, $onRejected);
    }
    /**
     * @param callable $onRejected
     */
    public function otherwise($onRejected): PromiseInterface
    {
        return $this->result->otherwise($onRejected);
    }
    /**
     * @param bool $unwrap
     */
    public function wait($unwrap = \true)
    {
        return $this->result->wait($unwrap);
    }
    public function getState(): string
    {
        return $this->result->getState();
    }
    public function resolve($value): void
    {
        $this->result->resolve($value);
    }
    public function reject($reason): void
    {
        $this->result->reject($reason);
    }
    public function cancel(): void
    {
        $this->currentPromise->cancel();
        $this->result->cancel();
    }
    private function nextCoroutine($yielded): void
    {
        $this->currentPromise = Create::promiseFor($yielded)->then([$this, '_handleSuccess'], [$this, '_handleFailure']);
    }
    public function _handleSuccess($value): void
    {
        unset($this->currentPromise);
        try {
            $next = $this->generator->send($value);
            if ($this->generator->valid()) {
                $this->nextCoroutine($next);
            } else {
                $this->result->resolve($value);
            }
        } catch (Throwable $throwable) {
            $this->result->reject($throwable);
        }
    }
    public function _handleFailure($reason): void
    {
        unset($this->currentPromise);
        try {
            $nextYield = $this->generator->throw(Create::exceptionFor($reason));
            $this->nextCoroutine($nextYield);
        } catch (Throwable $throwable) {
            $this->result->reject($throwable);
        }
    }
}
