<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Promise;

use InvalidArgumentException;
use Throwable;
use LogicException;
class RejectedPromise implements PromiseInterface
{
    private $reason;
    public function __construct($reason)
    {
        if (is_object($reason) && method_exists($reason, 'then')) {
            throw new InvalidArgumentException('You cannot create a RejectedPromise with a promise.');
        }
        $this->reason = $reason;
    }
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     */
    public function then($onFulfilled = null, $onRejected = null): PromiseInterface
    {
        if (!$onRejected) {
            return $this;
        }
        $queue = Utils::queue();
        $reason = $this->reason;
        $p = new Promise([$queue, 'run']);
        $queue->add(static function () use ($p, $reason, $onRejected): void {
            if (Is::pending($p)) {
                try {
                    $p->resolve($onRejected($reason));
                } catch (Throwable $e) {
                    $p->reject($e);
                }
            }
        });
        return $p;
    }
    /**
     * @param callable $onRejected
     */
    public function otherwise($onRejected): PromiseInterface
    {
        return $this->then(null, $onRejected);
    }
    /**
     * @param bool $unwrap
     */
    public function wait($unwrap = \true)
    {
        if ($unwrap) {
            throw Create::exceptionFor($this->reason);
        }
        return null;
    }
    public function getState(): string
    {
        return self::REJECTED;
    }
    public function resolve($value): void
    {
        throw new LogicException('Cannot resolve a rejected promise');
    }
    public function reject($reason): void
    {
        if ($reason !== $this->reason) {
            throw new LogicException('Cannot reject a rejected promise');
        }
    }
    public function cancel(): void
    {
    }
}
