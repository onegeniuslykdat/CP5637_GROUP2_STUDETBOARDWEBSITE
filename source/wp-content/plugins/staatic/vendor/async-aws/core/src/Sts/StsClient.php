<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts;

use Staatic\Vendor\AsyncAws\Core\AbstractApi;
use Staatic\Vendor\AsyncAws\Core\AwsError\AwsErrorFactoryInterface;
use Staatic\Vendor\AsyncAws\Core\AwsError\XmlAwsErrorFactory;
use Staatic\Vendor\AsyncAws\Core\RequestContext;
use Staatic\Vendor\AsyncAws\Core\Sts\Exception\ExpiredTokenException;
use Staatic\Vendor\AsyncAws\Core\Sts\Exception\IDPCommunicationErrorException;
use Staatic\Vendor\AsyncAws\Core\Sts\Exception\IDPRejectedClaimException;
use Staatic\Vendor\AsyncAws\Core\Sts\Exception\InvalidIdentityTokenException;
use Staatic\Vendor\AsyncAws\Core\Sts\Exception\MalformedPolicyDocumentException;
use Staatic\Vendor\AsyncAws\Core\Sts\Exception\PackedPolicyTooLargeException;
use Staatic\Vendor\AsyncAws\Core\Sts\Exception\RegionDisabledException;
use Staatic\Vendor\AsyncAws\Core\Sts\Input\AssumeRoleRequest;
use Staatic\Vendor\AsyncAws\Core\Sts\Input\AssumeRoleWithWebIdentityRequest;
use Staatic\Vendor\AsyncAws\Core\Sts\Input\GetCallerIdentityRequest;
use Staatic\Vendor\AsyncAws\Core\Sts\Result\AssumeRoleResponse;
use Staatic\Vendor\AsyncAws\Core\Sts\Result\AssumeRoleWithWebIdentityResponse;
use Staatic\Vendor\AsyncAws\Core\Sts\Result\GetCallerIdentityResponse;
use Staatic\Vendor\AsyncAws\Core\Sts\ValueObject\PolicyDescriptorType;
use Staatic\Vendor\AsyncAws\Core\Sts\ValueObject\ProvidedContext;
use Staatic\Vendor\AsyncAws\Core\Sts\ValueObject\Tag;
class StsClient extends AbstractApi
{
    public function assumeRole($input): AssumeRoleResponse
    {
        $input = AssumeRoleRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'AssumeRole', 'region' => $input->getRegion(), 'exceptionMapping' => ['MalformedPolicyDocument' => MalformedPolicyDocumentException::class, 'PackedPolicyTooLarge' => PackedPolicyTooLargeException::class, 'RegionDisabledException' => RegionDisabledException::class, 'ExpiredTokenException' => ExpiredTokenException::class]]));
        return new AssumeRoleResponse($response);
    }
    public function assumeRoleWithWebIdentity($input): AssumeRoleWithWebIdentityResponse
    {
        $input = AssumeRoleWithWebIdentityRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'AssumeRoleWithWebIdentity', 'region' => $input->getRegion(), 'exceptionMapping' => ['MalformedPolicyDocument' => MalformedPolicyDocumentException::class, 'PackedPolicyTooLarge' => PackedPolicyTooLargeException::class, 'IDPRejectedClaim' => IDPRejectedClaimException::class, 'IDPCommunicationError' => IDPCommunicationErrorException::class, 'InvalidIdentityToken' => InvalidIdentityTokenException::class, 'ExpiredTokenException' => ExpiredTokenException::class, 'RegionDisabledException' => RegionDisabledException::class]]));
        return new AssumeRoleWithWebIdentityResponse($response);
    }
    public function getCallerIdentity($input = []): GetCallerIdentityResponse
    {
        $input = GetCallerIdentityRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'GetCallerIdentity', 'region' => $input->getRegion()]));
        return new GetCallerIdentityResponse($response);
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
            return ['endpoint' => 'https://sts.amazonaws.com', 'signRegion' => 'us-east-1', 'signService' => 'sts', 'signVersions' => ['v4']];
        }
        switch ($region) {
            case 'cn-north-1':
            case 'cn-northwest-1':
                return ['endpoint' => "https://sts.{$region}.amazonaws.com.cn", 'signRegion' => $region, 'signService' => 'sts', 'signVersions' => ['v4']];
            case 'us-east-1-fips':
                return ['endpoint' => 'https://sts-fips.us-east-1.amazonaws.com', 'signRegion' => 'us-east-1', 'signService' => 'sts', 'signVersions' => ['v4']];
            case 'us-east-2-fips':
                return ['endpoint' => 'https://sts-fips.us-east-2.amazonaws.com', 'signRegion' => 'us-east-2', 'signService' => 'sts', 'signVersions' => ['v4']];
            case 'us-west-1-fips':
                return ['endpoint' => 'https://sts-fips.us-west-1.amazonaws.com', 'signRegion' => 'us-west-1', 'signService' => 'sts', 'signVersions' => ['v4']];
            case 'us-west-2-fips':
                return ['endpoint' => 'https://sts-fips.us-west-2.amazonaws.com', 'signRegion' => 'us-west-2', 'signService' => 'sts', 'signVersions' => ['v4']];
            case 'us-gov-east-1-fips':
                return ['endpoint' => 'https://sts.us-gov-east-1.amazonaws.com', 'signRegion' => 'us-gov-east-1', 'signService' => 'sts', 'signVersions' => ['v4']];
            case 'us-gov-west-1-fips':
                return ['endpoint' => 'https://sts.us-gov-west-1.amazonaws.com', 'signRegion' => 'us-gov-west-1', 'signService' => 'sts', 'signVersions' => ['v4']];
            case 'us-iso-east-1':
            case 'us-iso-west-1':
                return ['endpoint' => "https://sts.{$region}.c2s.ic.gov", 'signRegion' => $region, 'signService' => 'sts', 'signVersions' => ['v4']];
            case 'us-isob-east-1':
                return ['endpoint' => 'https://sts.us-isob-east-1.sc2s.sgov.gov', 'signRegion' => 'us-isob-east-1', 'signService' => 'sts', 'signVersions' => ['v4']];
        }
        return ['endpoint' => "https://sts.{$region}.amazonaws.com", 'signRegion' => $region, 'signService' => 'sts', 'signVersions' => ['v4']];
    }
    protected function getServiceCode(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);
        return 'sts';
    }
    protected function getSignatureScopeName(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);
        return 'sts';
    }
    protected function getSignatureVersion(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);
        return 'v4';
    }
}
