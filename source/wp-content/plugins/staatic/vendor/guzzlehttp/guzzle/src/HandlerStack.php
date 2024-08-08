<?php

namespace Staatic\Vendor\GuzzleHttp;

use LogicException;
use InvalidArgumentException;
use Staatic\Vendor\GuzzleHttp\Promise\PromiseInterface;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
class HandlerStack
{
    private $handler;
    private $stack = [];
    private $cached;
    /**
     * @param callable|null $handler
     */
    public static function create($handler = null): self
    {
        $stack = new self($handler ?: Utils::chooseHandler());
        $stack->push(Middleware::httpErrors(), 'http_errors');
        $stack->push(Middleware::redirect(), 'allow_redirects');
        $stack->push(Middleware::cookies(), 'cookies');
        $stack->push(Middleware::prepareBody(), 'prepare_body');
        return $stack;
    }
    public function __construct(callable $handler = null)
    {
        $this->handler = $handler;
    }
    public function __invoke(RequestInterface $request, array $options)
    {
        $handler = $this->resolve();
        return $handler($request, $options);
    }
    public function __toString()
    {
        $depth = 0;
        $stack = [];
        if ($this->handler !== null) {
            $stack[] = '0) Handler: ' . $this->debugCallable($this->handler);
        }
        $result = '';
        foreach (\array_reverse($this->stack) as $tuple) {
            ++$depth;
            $str = "{$depth}) Name: '{$tuple[1]}', ";
            $str .= 'Function: ' . $this->debugCallable($tuple[0]);
            $result = "> {$str}\n{$result}";
            $stack[] = $str;
        }
        foreach (\array_keys($stack) as $k) {
            $result .= "< {$stack[$k]}\n";
        }
        return $result;
    }
    /**
     * @param callable $handler
     */
    public function setHandler($handler): void
    {
        $this->handler = $handler;
        $this->cached = null;
    }
    public function hasHandler(): bool
    {
        return $this->handler !== null;
    }
    /**
     * @param callable $middleware
     * @param string|null $name
     */
    public function unshift($middleware, $name = null): void
    {
        \array_unshift($this->stack, [$middleware, $name]);
        $this->cached = null;
    }
    /**
     * @param callable $middleware
     * @param string $name
     */
    public function push($middleware, $name = ''): void
    {
        $this->stack[] = [$middleware, $name];
        $this->cached = null;
    }
    /**
     * @param string $findName
     * @param callable $middleware
     * @param string $withName
     */
    public function before($findName, $middleware, $withName = ''): void
    {
        $this->splice($findName, $withName, $middleware, \true);
    }
    /**
     * @param string $findName
     * @param callable $middleware
     * @param string $withName
     */
    public function after($findName, $middleware, $withName = ''): void
    {
        $this->splice($findName, $withName, $middleware, \false);
    }
    public function remove($remove): void
    {
        if (!is_string($remove) && !is_callable($remove)) {
            trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a callable or string to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }
        $this->cached = null;
        $idx = \is_callable($remove) ? 0 : 1;
        $this->stack = \array_values(\array_filter($this->stack, static function ($tuple) use ($idx, $remove) {
            return $tuple[$idx] !== $remove;
        }));
    }
    public function resolve(): callable
    {
        if ($this->cached === null) {
            if (($prev = $this->handler) === null) {
                throw new LogicException('No handler has been specified');
            }
            foreach (\array_reverse($this->stack) as $fn) {
                $prev = $fn[0]($prev);
            }
            $this->cached = $prev;
        }
        return $this->cached;
    }
    private function findByName(string $name): int
    {
        foreach ($this->stack as $k => $v) {
            if ($v[1] === $name) {
                return $k;
            }
        }
        throw new InvalidArgumentException("Middleware not found: {$name}");
    }
    private function splice(string $findName, string $withName, callable $middleware, bool $before): void
    {
        $this->cached = null;
        $idx = $this->findByName($findName);
        $tuple = [$middleware, $withName];
        if ($before) {
            if ($idx === 0) {
                \array_unshift($this->stack, $tuple);
            } else {
                $replacement = [$tuple, $this->stack[$idx]];
                \array_splice($this->stack, $idx, 1, $replacement);
            }
        } elseif ($idx === \count($this->stack) - 1) {
            $this->stack[] = $tuple;
        } else {
            $replacement = [$this->stack[$idx], $tuple];
            \array_splice($this->stack, $idx, 1, $replacement);
        }
    }
    private function debugCallable($fn): string
    {
        if (\is_string($fn)) {
            return "callable({$fn})";
        }
        if (\is_array($fn)) {
            return \is_string($fn[0]) ? "callable({$fn[0]}::{$fn[1]})" : ("callable(['" . \get_class($fn[0]) . "', '{$fn[1]}'])");
        }
        return 'callable(' . \spl_object_hash($fn) . ')';
    }
}
