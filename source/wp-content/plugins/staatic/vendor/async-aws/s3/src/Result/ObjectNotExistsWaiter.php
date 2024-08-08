<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use Staatic\Vendor\AsyncAws\Core\Exception\Http\HttpException;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Waiter;
use Staatic\Vendor\AsyncAws\S3\Input\HeadObjectRequest;
use Staatic\Vendor\AsyncAws\S3\S3Client;
class ObjectNotExistsWaiter extends Waiter
{
    protected const WAIT_TIMEOUT = 100.0;
    protected const WAIT_DELAY = 5.0;
    /**
     * @param Response $response
     * @param HttpException|null $exception
     */
    protected function extractState($response, $exception): string
    {
        if (404 === $response->getStatusCode()) {
            return self::STATE_SUCCESS;
        }
        return (null === $exception) ? self::STATE_PENDING : self::STATE_FAILURE;
    }
    protected function refreshState(): Waiter
    {
        if (!$this->awsClient instanceof S3Client) {
            throw new InvalidArgument('missing client injected in waiter result');
        }
        if (!$this->input instanceof HeadObjectRequest) {
            throw new InvalidArgument('missing last request injected in waiter result');
        }
        return $this->awsClient->objectNotExists($this->input);
    }
}
