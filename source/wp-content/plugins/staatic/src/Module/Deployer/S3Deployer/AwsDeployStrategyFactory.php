<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\Framework\DeployStrategy\AwsDeployStrategy;
use Staatic\Framework\DeployStrategy\DeployStrategyInterface;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\WordPress\Factory\HttpClientFactory;
use Staatic\WordPress\Publication\Publication;

final class AwsDeployStrategyFactory
{
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;

    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    /**
     * @var HttpClientFactory
     */
    private $httpClientFactory;

    public function __construct(ResultRepositoryInterface $resultRepository, ResourceRepositoryInterface $resourceRepository, HttpClientFactory $httpClientFactory)
    {
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
        $this->httpClientFactory = $httpClientFactory;
    }

    public function __invoke(Publication $publication): DeployStrategyInterface
    {
        return new AwsDeployStrategy(
            $this->resultRepository,
            $this->resourceRepository,
            $this->httpClientFactory->createSymfonyClient(),
            $this->options(
            $publication
        )
        );
    }

    private function options(Publication $publication): array
    {
        $bucketPrefix = get_option('staatic_aws_s3_prefix') ?: null;
        $retainPaths = RetainPaths::resolve(get_option('staatic_aws_retain_paths') ?: null, $bucketPrefix);
        $retainPaths = apply_filters('staatic_aws_retain_paths', $retainPaths);

        return [
            'basePath' => $publication->build()->destinationUrl()->getPath(),
            'endpoint' => get_option('staatic_aws_endpoint') ?: null,
            'region' => get_option('staatic_aws_s3_region') ?: 'us-east-1',
            'concurrency' => get_option('staatic_http_concurrency'),
            'profile' => get_option('staatic_aws_auth_profile') ?: null,
            'accessKeyId' => get_option('staatic_aws_auth_access_key_id') ?: null,
            'secretAccessKey' => get_option('staatic_aws_auth_secret_access_key') ?: null,
            'bucket' => get_option('staatic_aws_s3_bucket'),
            'prefix' => $bucketPrefix,
            'objectAcl' => get_option('staatic_aws_s3_object_acl') ?: null,
            'retainPaths' => $retainPaths,
            'distributionId' => get_option('staatic_aws_cloudfront_distribution_id') ?: null,
            'maxInvalidationPaths' => get_option('staatic_aws_cloudfront_max_invalidation_paths') ?: null,
            'invalidateEverythingPath' => get_option('staatic_aws_cloudfront_invalidate_everything_path') ?: null
        ];
    }
}
