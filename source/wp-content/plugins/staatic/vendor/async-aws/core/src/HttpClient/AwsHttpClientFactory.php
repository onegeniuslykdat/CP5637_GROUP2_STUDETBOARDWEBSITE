<?php

namespace Staatic\Vendor\AsyncAws\Core\HttpClient;

use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\HttpClient;
use Staatic\Vendor\Symfony\Component\HttpClient\RetryableHttpClient;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
class AwsHttpClientFactory
{
    /**
     * @param HttpClientInterface|null $httpClient
     * @param LoggerInterface|null $logger
     */
    public static function createRetryableClient($httpClient = null, $logger = null): HttpClientInterface
    {
        if (null === $httpClient) {
            $httpClient = HttpClient::create();
        }
        if (class_exists(RetryableHttpClient::class)) {
            $httpClient = new RetryableHttpClient($httpClient, new AwsRetryStrategy(), 3, $logger);
        }
        return $httpClient;
    }
}
