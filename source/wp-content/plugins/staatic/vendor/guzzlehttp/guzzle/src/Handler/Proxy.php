<?php

namespace Staatic\Vendor\GuzzleHttp\Handler;

use Staatic\Vendor\GuzzleHttp\Promise\PromiseInterface;
use Staatic\Vendor\GuzzleHttp\RequestOptions;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
class Proxy
{
    /**
     * @param callable $default
     * @param callable $sync
     */
    public static function wrapSync($default, $sync): callable
    {
        return static function (RequestInterface $request, array $options) use ($default, $sync): PromiseInterface {
            return empty($options[RequestOptions::SYNCHRONOUS]) ? $default($request, $options) : $sync($request, $options);
        };
    }
    /**
     * @param callable $default
     * @param callable $streaming
     */
    public static function wrapStreaming($default, $streaming): callable
    {
        return static function (RequestInterface $request, array $options) use ($default, $streaming): PromiseInterface {
            return empty($options['stream']) ? $default($request, $options) : $streaming($request, $options);
        };
    }
}
