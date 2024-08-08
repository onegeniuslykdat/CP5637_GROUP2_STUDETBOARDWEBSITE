<?php

namespace Staatic\Vendor\AsyncAws\CloudFront;

use Staatic\Vendor\AsyncAws\CloudFront\Exception\AccessDeniedException;
use Staatic\Vendor\AsyncAws\CloudFront\Exception\BatchTooLargeException;
use Staatic\Vendor\AsyncAws\CloudFront\Exception\InconsistentQuantitiesException;
use Staatic\Vendor\AsyncAws\CloudFront\Exception\InvalidArgumentException;
use Staatic\Vendor\AsyncAws\CloudFront\Exception\MissingBodyException;
use Staatic\Vendor\AsyncAws\CloudFront\Exception\NoSuchDistributionException;
use Staatic\Vendor\AsyncAws\CloudFront\Exception\TooManyInvalidationsInProgressException;
use Staatic\Vendor\AsyncAws\CloudFront\Input\CreateInvalidationRequest;
use Staatic\Vendor\AsyncAws\CloudFront\Result\CreateInvalidationResult;
use Staatic\Vendor\AsyncAws\CloudFront\ValueObject\InvalidationBatch;
use Staatic\Vendor\AsyncAws\Core\AbstractApi;
use Staatic\Vendor\AsyncAws\Core\AwsError\AwsErrorFactoryInterface;
use Staatic\Vendor\AsyncAws\Core\AwsError\XmlAwsErrorFactory;
use Staatic\Vendor\AsyncAws\Core\RequestContext;
class CloudFrontClient extends AbstractApi
{
    public function createInvalidation($input): CreateInvalidationResult
    {
        $input = CreateInvalidationRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'CreateInvalidation', 'region' => $input->getRegion(), 'exceptionMapping' => ['AccessDenied' => AccessDeniedException::class, 'MissingBody' => MissingBodyException::class, 'InvalidArgument' => InvalidArgumentException::class, 'NoSuchDistribution' => NoSuchDistributionException::class, 'BatchTooLarge' => BatchTooLargeException::class, 'TooManyInvalidationsInProgress' => TooManyInvalidationsInProgressException::class, 'InconsistentQuantities' => InconsistentQuantitiesException::class]]));
        return new CreateInvalidationResult($response);
    }
    protected function getAwsErrorFactory(): AwsErrorFactoryInterface
    {
        return new XmlAwsErrorFactory();
    }
    /**
     * @param string|null $region
     */
    protected function getEndpointMetadata($region): array
    {
        if (null === $region) {
            return ['endpoint' => 'https://cloudfront.amazonaws.com', 'signRegion' => 'us-east-1', 'signService' => 'cloudfront', 'signVersions' => ['v4']];
        }
        switch ($region) {
            case 'cn-north-1':
            case 'cn-northwest-1':
                return ['endpoint' => 'https://cloudfront.cn-northwest-1.amazonaws.com.cn', 'signRegion' => 'cn-northwest-1', 'signService' => 'cloudfront', 'signVersions' => ['v4']];
        }
        return ['endpoint' => 'https://cloudfront.amazonaws.com', 'signRegion' => 'us-east-1', 'signService' => 'cloudfront', 'signVersions' => ['v4']];
    }
}
