<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Promise;

use InvalidArgumentException;
use Throwable;
use LogicException;
class FulfilledPromise implements PromiseInterface
{
    private $value;
    public function __construct($value)
    {
        if (is_object($value) && method_exists($value, 'then')) {
            throw new InvalidArgumentException('You cannot create a FulfilledPromise with a promise.');
        }
        $this->value = $value;
    }
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     */
    public function then($onFulfilled = null, $onRejected = null): PromiseInterface
    {
        if (!$onFulfilled) {
            return $this;
        }
        $queue = Utils::queue();
        $p = new Promise([$queue, 'run']);
        $value = $this->value;
        $queue->add(static function () use ($p, $value, $onFulfilled): void {
            if (Is::pending($p)) {
                try {
                    $p->resolve($onFulfilled($value));
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
        return $unwrap ? $this->value : null;
    }
    public function getState(): string
    {
        return self::FULFILLED;
    }
    public function resolve($value): void
    {
        if ($value !== $this->value) {
            throw new LogicException('Cannot resolve a fulfilled promise');
        }
    }
    public function reject($reason): void
    {
        throw new LogicException('Cannot reject a fulfilled promise');
    }
    public function cancel(): void
    {
    }
}
