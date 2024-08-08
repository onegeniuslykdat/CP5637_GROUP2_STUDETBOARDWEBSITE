<?php

namespace Staatic\Vendor\AsyncAws\S3;

use Staatic\Vendor\AsyncAws\Core\AbstractApi;
use Staatic\Vendor\AsyncAws\Core\AwsError\AwsErrorFactoryInterface;
use Staatic\Vendor\AsyncAws\Core\AwsError\XmlAwsErrorFactory;
use Staatic\Vendor\AsyncAws\Core\Configuration;
use Staatic\Vendor\AsyncAws\Core\RequestContext;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\BucketCannedACL;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumMode;
use Staatic\Vendor\AsyncAws\S3\Enum\EncodingType;
use Staatic\Vendor\AsyncAws\S3\Enum\MetadataDirective;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectCannedACL;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectLockLegalHoldStatus;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectLockMode;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectOwnership;
use Staatic\Vendor\AsyncAws\S3\Enum\OptionalObjectAttributes;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestPayer;
use Staatic\Vendor\AsyncAws\S3\Enum\ServerSideEncryption;
use Staatic\Vendor\AsyncAws\S3\Enum\StorageClass;
use Staatic\Vendor\AsyncAws\S3\Enum\TaggingDirective;
use Staatic\Vendor\AsyncAws\S3\Exception\BucketAlreadyExistsException;
use Staatic\Vendor\AsyncAws\S3\Exception\BucketAlreadyOwnedByYouException;
use Staatic\Vendor\AsyncAws\S3\Exception\InvalidObjectStateException;
use Staatic\Vendor\AsyncAws\S3\Exception\NoSuchBucketException;
use Staatic\Vendor\AsyncAws\S3\Exception\NoSuchKeyException;
use Staatic\Vendor\AsyncAws\S3\Exception\NoSuchUploadException;
use Staatic\Vendor\AsyncAws\S3\Exception\ObjectNotInActiveTierErrorException;
use Staatic\Vendor\AsyncAws\S3\Input\AbortMultipartUploadRequest;
use Staatic\Vendor\AsyncAws\S3\Input\CompleteMultipartUploadRequest;
use Staatic\Vendor\AsyncAws\S3\Input\CopyObjectRequest;
use Staatic\Vendor\AsyncAws\S3\Input\CreateBucketRequest;
use Staatic\Vendor\AsyncAws\S3\Input\CreateMultipartUploadRequest;
use Staatic\Vendor\AsyncAws\S3\Input\DeleteBucketCorsRequest;
use Staatic\Vendor\AsyncAws\S3\Input\DeleteBucketRequest;
use Staatic\Vendor\AsyncAws\S3\Input\DeleteObjectRequest;
use Staatic\Vendor\AsyncAws\S3\Input\DeleteObjectsRequest;
use Staatic\Vendor\AsyncAws\S3\Input\DeleteObjectTaggingRequest;
use Staatic\Vendor\AsyncAws\S3\Input\GetBucketCorsRequest;
use Staatic\Vendor\AsyncAws\S3\Input\GetBucketEncryptionRequest;
use Staatic\Vendor\AsyncAws\S3\Input\GetObjectAclRequest;
use Staatic\Vendor\AsyncAws\S3\Input\GetObjectRequest;
use Staatic\Vendor\AsyncAws\S3\Input\GetObjectTaggingRequest;
use Staatic\Vendor\AsyncAws\S3\Input\HeadBucketRequest;
use Staatic\Vendor\AsyncAws\S3\Input\HeadObjectRequest;
use Staatic\Vendor\AsyncAws\S3\Input\ListBucketsRequest;
use Staatic\Vendor\AsyncAws\S3\Input\ListMultipartUploadsRequest;
use Staatic\Vendor\AsyncAws\S3\Input\ListObjectsV2Request;
use Staatic\Vendor\AsyncAws\S3\Input\ListObjectVersionsRequest;
use Staatic\Vendor\AsyncAws\S3\Input\ListPartsRequest;
use Staatic\Vendor\AsyncAws\S3\Input\PutBucketCorsRequest;
use Staatic\Vendor\AsyncAws\S3\Input\PutBucketNotificationConfigurationRequest;
use Staatic\Vendor\AsyncAws\S3\Input\PutBucketTaggingRequest;
use Staatic\Vendor\AsyncAws\S3\Input\PutObjectAclRequest;
use Staatic\Vendor\AsyncAws\S3\Input\PutObjectRequest;
use Staatic\Vendor\AsyncAws\S3\Input\PutObjectTaggingRequest;
use Staatic\Vendor\AsyncAws\S3\Input\UploadPartCopyRequest;
use Staatic\Vendor\AsyncAws\S3\Input\UploadPartRequest;
use Staatic\Vendor\AsyncAws\S3\Result\AbortMultipartUploadOutput;
use Staatic\Vendor\AsyncAws\S3\Result\BucketExistsWaiter;
use Staatic\Vendor\AsyncAws\S3\Result\BucketNotExistsWaiter;
use Staatic\Vendor\AsyncAws\S3\Result\CompleteMultipartUploadOutput;
use Staatic\Vendor\AsyncAws\S3\Result\CopyObjectOutput;
use Staatic\Vendor\AsyncAws\S3\Result\CreateBucketOutput;
use Staatic\Vendor\AsyncAws\S3\Result\CreateMultipartUploadOutput;
use Staatic\Vendor\AsyncAws\S3\Result\DeleteObjectOutput;
use Staatic\Vendor\AsyncAws\S3\Result\DeleteObjectsOutput;
use Staatic\Vendor\AsyncAws\S3\Result\DeleteObjectTaggingOutput;
use Staatic\Vendor\AsyncAws\S3\Result\GetBucketCorsOutput;
use Staatic\Vendor\AsyncAws\S3\Result\GetBucketEncryptionOutput;
use Staatic\Vendor\AsyncAws\S3\Result\GetObjectAclOutput;
use Staatic\Vendor\AsyncAws\S3\Result\GetObjectOutput;
use Staatic\Vendor\AsyncAws\S3\Result\GetObjectTaggingOutput;
use Staatic\Vendor\AsyncAws\S3\Result\HeadObjectOutput;
use Staatic\Vendor\AsyncAws\S3\Result\ListBucketsOutput;
use Staatic\Vendor\AsyncAws\S3\Result\ListMultipartUploadsOutput;
use Staatic\Vendor\AsyncAws\S3\Result\ListObjectsV2Output;
use Staatic\Vendor\AsyncAws\S3\Result\ListObjectVersionsOutput;
use Staatic\Vendor\AsyncAws\S3\Result\ListPartsOutput;
use Staatic\Vendor\AsyncAws\S3\Result\ObjectExistsWaiter;
use Staatic\Vendor\AsyncAws\S3\Result\ObjectNotExistsWaiter;
use Staatic\Vendor\AsyncAws\S3\Result\PutObjectAclOutput;
use Staatic\Vendor\AsyncAws\S3\Result\PutObjectOutput;
use Staatic\Vendor\AsyncAws\S3\Result\PutObjectTaggingOutput;
use Staatic\Vendor\AsyncAws\S3\Result\UploadPartCopyOutput;
use Staatic\Vendor\AsyncAws\S3\Result\UploadPartOutput;
use Staatic\Vendor\AsyncAws\S3\Signer\SignerV4ForS3;
use Staatic\Vendor\AsyncAws\S3\ValueObject\AccessControlPolicy;
use Staatic\Vendor\AsyncAws\S3\ValueObject\CompletedMultipartUpload;
use Staatic\Vendor\AsyncAws\S3\ValueObject\CORSConfiguration;
use Staatic\Vendor\AsyncAws\S3\ValueObject\CreateBucketConfiguration;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Delete;
use Staatic\Vendor\AsyncAws\S3\ValueObject\MultipartUpload;
use Staatic\Vendor\AsyncAws\S3\ValueObject\NotificationConfiguration;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Part;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Tagging;
class S3Client extends AbstractApi
{
    public function abortMultipartUpload($input): AbortMultipartUploadOutput
    {
        $input = AbortMultipartUploadRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'AbortMultipartUpload', 'region' => $input->getRegion(), 'exceptionMapping' => ['NoSuchUpload' => NoSuchUploadException::class]]));
        return new AbortMultipartUploadOutput($response);
    }
    public function bucketExists($input): BucketExistsWaiter
    {
        $input = HeadBucketRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'HeadBucket', 'region' => $input->getRegion(), 'exceptionMapping' => ['NoSuchBucket' => NoSuchBucketException::class]]));
        return new BucketExistsWaiter($response, $this, $input);
    }
    public function bucketNotExists($input): BucketNotExistsWaiter
    {
        $input = HeadBucketRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'HeadBucket', 'region' => $input->getRegion(), 'exceptionMapping' => ['NoSuchBucket' => NoSuchBucketException::class]]));
        return new BucketNotExistsWaiter($response, $this, $input);
    }
    public function completeMultipartUpload($input): CompleteMultipartUploadOutput
    {
        $input = CompleteMultipartUploadRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'CompleteMultipartUpload', 'region' => $input->getRegion()]));
        return new CompleteMultipartUploadOutput($response);
    }
    public function copyObject($input): CopyObjectOutput
    {
        $input = CopyObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'CopyObject', 'region' => $input->getRegion(), 'exceptionMapping' => ['ObjectNotInActiveTierError' => ObjectNotInActiveTierErrorException::class]]));
        return new CopyObjectOutput($response);
    }
    public function createBucket($input): CreateBucketOutput
    {
        $input = CreateBucketRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'CreateBucket', 'region' => $input->getRegion(), 'exceptionMapping' => ['BucketAlreadyExists' => BucketAlreadyExistsException::class, 'BucketAlreadyOwnedByYou' => BucketAlreadyOwnedByYouException::class]]));
        return new CreateBucketOutput($response);
    }
    public function createMultipartUpload($input): CreateMultipartUploadOutput
    {
        $input = CreateMultipartUploadRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'CreateMultipartUpload', 'region' => $input->getRegion()]));
        return new CreateMultipartUploadOutput($response);
    }
    public function deleteBucket($input): Result
    {
        $input = DeleteBucketRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'DeleteBucket', 'region' => $input->getRegion()]));
        return new Result($response);
    }
    public function deleteBucketCors($input): Result
    {
        $input = DeleteBucketCorsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'DeleteBucketCors', 'region' => $input->getRegion()]));
        return new Result($response);
    }
    public function deleteObject($input): DeleteObjectOutput
    {
        $input = DeleteObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'DeleteObject', 'region' => $input->getRegion()]));
        return new DeleteObjectOutput($response);
    }
    public function deleteObjectTagging($input): DeleteObjectTaggingOutput
    {
        $input = DeleteObjectTaggingRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'DeleteObjectTagging', 'region' => $input->getRegion()]));
        return new DeleteObjectTaggingOutput($response);
    }
    public function deleteObjects($input): DeleteObjectsOutput
    {
        $input = DeleteObjectsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'DeleteObjects', 'region' => $input->getRegion()]));
        return new DeleteObjectsOutput($response);
    }
    public function getBucketCors($input): GetBucketCorsOutput
    {
        $input = GetBucketCorsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'GetBucketCors', 'region' => $input->getRegion()]));
        return new GetBucketCorsOutput($response);
    }
    public function getBucketEncryption($input): GetBucketEncryptionOutput
    {
        $input = GetBucketEncryptionRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'GetBucketEncryption', 'region' => $input->getRegion()]));
        return new GetBucketEncryptionOutput($response);
    }
    public function getObject($input): GetObjectOutput
    {
        $input = GetObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'GetObject', 'region' => $input->getRegion(), 'exceptionMapping' => ['NoSuchKey' => NoSuchKeyException::class, 'InvalidObjectState' => InvalidObjectStateException::class]]));
        return new GetObjectOutput($response);
    }
    public function getObjectAcl($input): GetObjectAclOutput
    {
        $input = GetObjectAclRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'GetObjectAcl', 'region' => $input->getRegion(), 'exceptionMapping' => ['NoSuchKey' => NoSuchKeyException::class]]));
        return new GetObjectAclOutput($response);
    }
    public function getObjectTagging($input): GetObjectTaggingOutput
    {
        $input = GetObjectTaggingRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'GetObjectTagging', 'region' => $input->getRegion()]));
        return new GetObjectTaggingOutput($response);
    }
    public function headObject($input): HeadObjectOutput
    {
        $input = HeadObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'HeadObject', 'region' => $input->getRegion(), 'exceptionMapping' => ['NoSuchKey' => NoSuchKeyException::class, 'http_status_code_404' => NoSuchKeyException::class]]));
        return new HeadObjectOutput($response);
    }
    public function listBuckets($input = []): ListBucketsOutput
    {
        $input = ListBucketsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'ListBuckets', 'region' => $input->getRegion()]));
        return new ListBucketsOutput($response);
    }
    public function listMultipartUploads($input): ListMultipartUploadsOutput
    {
        $input = ListMultipartUploadsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'ListMultipartUploads', 'region' => $input->getRegion()]));
        return new ListMultipartUploadsOutput($response, $this, $input);
    }
    public function listObjectVersions($input): ListObjectVersionsOutput
    {
        $input = ListObjectVersionsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'ListObjectVersions', 'region' => $input->getRegion()]));
        return new ListObjectVersionsOutput($response, $this, $input);
    }
    public function listObjectsV2($input): ListObjectsV2Output
    {
        $input = ListObjectsV2Request::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'ListObjectsV2', 'region' => $input->getRegion(), 'exceptionMapping' => ['NoSuchBucket' => NoSuchBucketException::class]]));
        return new ListObjectsV2Output($response, $this, $input);
    }
    public function listParts($input): ListPartsOutput
    {
        $input = ListPartsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'ListParts', 'region' => $input->getRegion()]));
        return new ListPartsOutput($response, $this, $input);
    }
    public function objectExists($input): ObjectExistsWaiter
    {
        $input = HeadObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'HeadObject', 'region' => $input->getRegion(), 'exceptionMapping' => ['NoSuchKey' => NoSuchKeyException::class]]));
        return new ObjectExistsWaiter($response, $this, $input);
    }
    public function objectNotExists($input): ObjectNotExistsWaiter
    {
        $input = HeadObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'HeadObject', 'region' => $input->getRegion(), 'exceptionMapping' => ['NoSuchKey' => NoSuchKeyException::class]]));
        return new ObjectNotExistsWaiter($response, $this, $input);
    }
    public function putBucketCors($input): Result
    {
        $input = PutBucketCorsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'PutBucketCors', 'region' => $input->getRegion()]));
        return new Result($response);
    }
    public function putBucketNotificationConfiguration($input): Result
    {
        $input = PutBucketNotificationConfigurationRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'PutBucketNotificationConfiguration', 'region' => $input->getRegion()]));
        return new Result($response);
    }
    public function putBucketTagging($input): Result
    {
        $input = PutBucketTaggingRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'PutBucketTagging', 'region' => $input->getRegion()]));
        return new Result($response);
    }
    public function putObject($input): PutObjectOutput
    {
        $input = PutObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'PutObject', 'region' => $input->getRegion()]));
        return new PutObjectOutput($response);
    }
    public function putObjectAcl($input): PutObjectAclOutput
    {
        $input = PutObjectAclRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'PutObjectAcl', 'region' => $input->getRegion(), 'exceptionMapping' => ['NoSuchKey' => NoSuchKeyException::class]]));
        return new PutObjectAclOutput($response);
    }
    public function putObjectTagging($input): PutObjectTaggingOutput
    {
        $input = PutObjectTaggingRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'PutObjectTagging', 'region' => $input->getRegion()]));
        return new PutObjectTaggingOutput($response);
    }
    public function uploadPart($input): UploadPartOutput
    {
        $input = UploadPartRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'UploadPart', 'region' => $input->getRegion()]));
        return new UploadPartOutput($response);
    }
    public function uploadPartCopy($input): UploadPartCopyOutput
    {
        $input = UploadPartCopyRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'UploadPartCopy', 'region' => $input->getRegion()]));
        return new UploadPartCopyOutput($response);
    }
    protected function getAwsErrorFactory(): AwsErrorFactoryInterface
    {
        return new XmlAwsErrorFactory();
    }
    /**
     * @param string $uri
     * @param mixed[] $query
     * @param string|null $region
     */
    protected function getEndpoint($uri, $query, $region): string
    {
        $uriParts = explode('/', $uri, 3);
        $bucket = explode('?', $uriParts[1] ?? '', 2)[0];
        $uriWithOutBucket = substr($uriParts[1] ?? '', \strlen($bucket)) . ($uriParts[2] ?? '');
        $bucketLen = \strlen($bucket);
        $configuration = $this->getConfiguration();
        if ($bucketLen < 3 || $bucketLen > 63 || filter_var($bucket, \FILTER_VALIDATE_IP) || !preg_match('/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?$/', $bucket) || filter_var(parse_url($configuration->get('endpoint'), \PHP_URL_HOST), \FILTER_VALIDATE_IP) || filter_var($configuration->get('pathStyleEndpoint'), \FILTER_VALIDATE_BOOLEAN)) {
            return parent::getEndpoint($uri, $query, $region);
        }
        return preg_replace('|https?://|', '${0}' . $bucket . '.', parent::getEndpoint('/' . $uriWithOutBucket, $query, $region));
    }
    /**
     * @param string|null $region
     */
    protected function getEndpointMetadata($region): array
    {
        if (null === $region) {
            return ['endpoint' => 'https://s3.amazonaws.com', 'signRegion' => 'us-east-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
        }
        switch ($region) {
            case 'af-south-1':
            case 'ap-east-1':
            case 'ap-northeast-1':
            case 'ap-northeast-2':
            case 'ap-northeast-3':
            case 'ap-south-1':
            case 'ap-south-2':
            case 'ap-southeast-1':
            case 'ap-southeast-2':
            case 'ap-southeast-3':
            case 'ap-southeast-4':
            case 'ca-central-1':
            case 'ca-west-1':
            case 'eu-central-1':
            case 'eu-central-2':
            case 'eu-north-1':
            case 'eu-south-1':
            case 'eu-south-2':
            case 'eu-west-1':
            case 'eu-west-2':
            case 'eu-west-3':
            case 'il-central-1':
            case 'me-central-1':
            case 'me-south-1':
            case 'sa-east-1':
            case 'us-east-1':
            case 'us-east-2':
            case 'us-gov-east-1':
            case 'us-gov-west-1':
            case 'us-west-1':
            case 'us-west-2':
                return ['endpoint' => "https://s3.{$region}.amazonaws.com", 'signRegion' => $region, 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'cn-north-1':
            case 'cn-northwest-1':
                return ['endpoint' => "https://s3.{$region}.amazonaws.com.cn", 'signRegion' => $region, 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 's3-external-1':
                return ['endpoint' => 'https://s3-external-1.amazonaws.com', 'signRegion' => 'us-east-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'fips-ca-central-1':
                return ['endpoint' => 'https://s3-fips.ca-central-1.amazonaws.com', 'signRegion' => 'ca-central-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'fips-ca-west-1':
                return ['endpoint' => 'https://s3-fips.ca-west-1.amazonaws.com', 'signRegion' => 'ca-west-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'fips-us-east-1':
                return ['endpoint' => 'https://s3-fips.us-east-1.amazonaws.com', 'signRegion' => 'us-east-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'fips-us-east-2':
                return ['endpoint' => 'https://s3-fips.us-east-2.amazonaws.com', 'signRegion' => 'us-east-2', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'fips-us-west-1':
                return ['endpoint' => 'https://s3-fips.us-west-1.amazonaws.com', 'signRegion' => 'us-west-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'fips-us-west-2':
                return ['endpoint' => 'https://s3-fips.us-west-2.amazonaws.com', 'signRegion' => 'us-west-2', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'fips-us-gov-east-1':
                return ['endpoint' => 'https://s3-fips.us-gov-east-1.amazonaws.com', 'signRegion' => 'us-gov-east-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'fips-us-gov-west-1':
                return ['endpoint' => 'https://s3-fips.us-gov-west-1.amazonaws.com', 'signRegion' => 'us-gov-west-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'us-iso-east-1':
            case 'us-iso-west-1':
                return ['endpoint' => "https://s3.{$region}.c2s.ic.gov", 'signRegion' => $region, 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'us-isob-east-1':
                return ['endpoint' => 'https://s3.us-isob-east-1.sc2s.sgov.gov', 'signRegion' => 'us-isob-east-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'fips-us-iso-east-1':
                return ['endpoint' => 'https://s3-fips.us-iso-east-1.c2s.ic.gov', 'signRegion' => 'us-iso-east-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'fips-us-iso-west-1':
                return ['endpoint' => 'https://s3-fips.us-iso-west-1.c2s.ic.gov', 'signRegion' => 'us-iso-west-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
            case 'fips-us-isob-east-1':
                return ['endpoint' => 'https://s3-fips.us-isob-east-1.sc2s.sgov.gov', 'signRegion' => 'us-isob-east-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
        }
        return ['endpoint' => 'https://s3.amazonaws.com', 'signRegion' => 'us-east-1', 'signService' => 's3', 'signVersions' => ['s3v4']];
    }
    protected function getServiceCode(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);
        return 's3';
    }
    protected function getSignatureScopeName(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);
        return 's3';
    }
    protected function getSignatureVersion(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);
        return 's3v4';
    }
    protected function getSignerFactories(): array
    {
        return ['s3v4' => function (string $service, string $region) {
            $configuration = $this->getConfiguration();
            $options = [];
            if (Configuration::optionExists('sendChunkedBody')) {
                $options['sendChunkedBody'] = filter_var($configuration->get('sendChunkedBody'), \FILTER_VALIDATE_BOOLEAN);
            }
            return new SignerV4ForS3($service, $region, $options);
        }] + parent::getSignerFactories();
    }
}
