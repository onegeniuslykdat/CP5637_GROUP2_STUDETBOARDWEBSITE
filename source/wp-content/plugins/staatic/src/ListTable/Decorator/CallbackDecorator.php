<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Decorator;

use Closure;

final class CallbackDecorator implements DecoratorInterface
{
    /**
     * @var Closure
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = Closure::fromCallable($callback);
    }

    /**
     * @param string $input
     */
    public function decorate($input, $item): string
    {
        return ($this->callback)($input, $item);
    }
}
