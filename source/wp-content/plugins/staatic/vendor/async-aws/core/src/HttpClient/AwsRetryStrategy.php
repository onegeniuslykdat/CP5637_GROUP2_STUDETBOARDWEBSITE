<?php

namespace Staatic\Vendor\AsyncAws\Core\HttpClient;

use Staatic\Vendor\AsyncAws\Core\AwsError\AwsErrorFactoryInterface;
use Staatic\Vendor\AsyncAws\Core\AwsError\ChainAwsErrorFactory;
use Staatic\Vendor\AsyncAws\Core\Exception\UnparsableResponse;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\AsyncContext;
use Staatic\Vendor\Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
class AwsRetryStrategy extends GenericRetryStrategy
{
    public const DEFAULT_RETRY_STATUS_CODES = [0, 423, 425, 429, 500, 502, 503, 504, 507, 510];
    private $awsErrorFactory;
    public function __construct(array $statusCodes = self::DEFAULT_RETRY_STATUS_CODES, int $delayMs = 1000, float $multiplier = 2.0, int $maxDelayMs = 0, float $jitter = 0.1, ?AwsErrorFactoryInterface $awsErrorFactory = null)
    {
        parent::__construct($statusCodes, $delayMs, $multiplier, $maxDelayMs, $jitter);
        $this->awsErrorFactory = $awsErrorFactory ?? new ChainAwsErrorFactory();
    }
    /**
     * @param AsyncContext $context
     * @param string|null $responseContent
     * @param TransportExceptionInterface|null $exception
     */
    public function shouldRetry($context, $responseContent, $exception): ?bool
    {
        if (parent::shouldRetry($context, $responseContent, $exception)) {
            return \true;
        }
        if (!\in_array($context->getStatusCode(), [400, 403], \true)) {
            return \false;
        }
        if (null === $responseContent) {
            return null;
        }
        try {
            $error = $this->awsErrorFactory->createFromContent($responseContent, $context->getHeaders());
        } catch (UnparsableResponse $e) {
            return \false;
        }
        return \in_array($error->getCode(), ['RequestLimitExceeded', 'Throttling', 'ThrottlingException', 'ThrottledException', 'LimitExceededException', 'PriorRequestNotComplete', 'ProvisionedThroughputExceededException', 'RequestThrottled', 'SlowDown', 'BandwidthLimitExceeded', 'RequestThrottledException', 'RetryableThrottlingException', 'TooManyRequestsException', 'IDPCommunicationError', 'EC2ThrottledException', 'TransactionInProgressException'], \true);
    }
}
