<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Retry;

use Staatic\Vendor\Symfony\Component\HttpClient\Response\AsyncContext;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
interface RetryStrategyInterface
{
    /**
     * @param AsyncContext $context
     * @param string|null $responseContent
     * @param TransportExceptionInterface|null $exception
     */
    public function shouldRetry($context, $responseContent, $exception): ?bool;
    /**
     * @param AsyncContext $context
     * @param string|null $responseContent
     * @param TransportExceptionInterface|null $exception
     */
    public function getDelay($context, $responseContent, $exception): int;
}
