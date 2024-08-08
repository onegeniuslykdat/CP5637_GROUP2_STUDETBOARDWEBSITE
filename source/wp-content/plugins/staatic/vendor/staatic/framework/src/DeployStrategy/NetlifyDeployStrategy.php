<?php

namespace Staatic\Framework\DeployStrategy;

use Generator;
use Staatic\Vendor\GuzzleHttp\ClientInterface;
use Staatic\Vendor\GuzzleHttp\Pool;
use Staatic\Vendor\GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
use Staatic\Framework\Deployment;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\Result;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\Framework\Util\PathEncoder;
use Staatic\Framework\Util\PathHelper;
use Throwable;
final class NetlifyDeployStrategy implements DeployStrategyInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    private const API_URL = 'https://api.netlify.com/api/v1';
    public const FINISH_REFRESH_INTERVAL = 5;
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;
    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;
    /**
     * @var ClientInterface
     */
    private $httpClient;
    /**
     * @var string
     */
    private $basePath = '';
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var string
     */
    private $siteId;
    /**
     * @var int
     */
    private $concurrency;
    /**
     * @var mixed[]
     */
    private $loggerContext = [];
    public function __construct(ResultRepositoryInterface $resultRepository, ResourceRepositoryInterface $resourceRepository, ClientInterface $httpClient, array $options = [])
    {
        $this->logger = new NullLogger();
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
        $this->httpClient = $httpClient;
        if (!empty($options['basePath'])) {
            $this->basePath = rtrim($options['basePath'], '/');
        }
        if (empty($options['accessToken'])) {
            throw new InvalidArgumentException('Missing required option "accessToken"');
        }
        if (empty($options['siteId'])) {
            throw new InvalidArgumentException('Missing required option "siteId"');
        }
        $this->accessToken = $options['accessToken'];
        $this->siteId = $options['siteId'];
        $this->concurrency = $options['concurrency'] ?? 4;
    }
    /**
     * @param Deployment $deployment
     */
    public function initiate($deployment): array
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $this->logger->info('Initiating deployment', $this->loggerContext);
        $manifest = $this->createManifest($deployment->buildId(), $deployment->id());
        $remoteDeployment = $this->createRemoteDeployment($manifest);
        $requiredFiles = array_filter($manifest, function (Result $result) use ($remoteDeployment) {
            return in_array($result->sha1(), $remoteDeployment['required']);
        });
        $requiredFiles = array_map(function (Result $result) {
            return $result->id();
        }, $requiredFiles);
        $nonRequiredFiles = array_filter($manifest, function (Result $result) use ($remoteDeployment) {
            return !in_array($result->sha1(), $remoteDeployment['required']);
        });
        $resultIds = [];
        foreach ($nonRequiredFiles as $result) {
            $resultIds[] = $result->id();
            if (count($resultIds) >= 100) {
                $this->resultRepository->markManyDeployed($deployment->id(), $resultIds);
                $resultIds = [];
            }
        }
        if (count($resultIds)) {
            $this->resultRepository->markManyDeployed($deployment->id(), $resultIds);
        }
        $this->logger->info(sprintf('Deployment initiated (remote id: "%s", all files: %d, required files: "%s")', $remoteDeployment['id'], count($manifest), implode('", "', array_keys($requiredFiles))), $this->loggerContext);
        return ['deploymentId' => $remoteDeployment['id'], 'requiredFiles' => $requiredFiles];
    }
    /**
     * @param Deployment $deployment
     * @param iterable $results
     */
    public function processResults($deployment, $results): void
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $this->logger->info('Deploying results', $this->loggerContext);
        $metadata = $deployment->metadata();
        $pool = new Pool($this->httpClient, $this->getUploadRequests($metadata['deploymentId'], $results), ['concurrency' => $this->concurrency, 'fulfilled' => function (ResponseInterface $response, string $resultId) {
            $this->logger->info("Deployment of result #{$resultId} was successful", array_merge($this->loggerContext, ['resultId' => $resultId]));
        }, 'rejected' => function (Throwable $transferException, string $resultId) {
            $this->logger->error("Deployment of result #{$resultId} failed: {$transferException->getMessage()}", array_merge($this->loggerContext, ['resultId' => $resultId]));
        }]);
        $promise = $pool->promise();
        $promise->wait();
    }
    /**
     * @param Deployment $deployment
     */
    public function finish($deployment): bool
    {
        $this->logger->info('Finishing deployment', $this->loggerContext);
        $deploymentMetadata = $deployment->metadata();
        $result = $this->apiRequest("/deploys/{$deploymentMetadata['deploymentId']}");
        if (!empty($result['required'])) {
            throw new RuntimeException(sprintf('Remote deployment still awaits required files: "%s"', implode('", "', $result['required'])));
        }
        $isFinished = !in_array($result['state'], ['enqueued', 'building', 'uploading', 'uploaded', 'preparing', 'prepared', 'processing', 'processed', 'retrying'], \true);
        if ($isFinished) {
            foreach ($result['summary']['messages'] ?? [] as $message) {
                $this->logger->info("Netlify: {$message['title']}");
            }
            if (!empty($result['deploy_ssl_url'])) {
                $this->logger->notice("Deploy URL: {$result['deploy_ssl_url']}");
            }
        } else {
            sleep(self::FINISH_REFRESH_INTERVAL);
        }
        return $isFinished;
    }
    private function determineFilePath(string $path): string
    {
        if ($this->basePath && strncmp($path, $this->basePath, strlen($this->basePath)) === 0) {
            $path = mb_substr($path, mb_strlen($this->basePath));
        }
        return PathHelper::determineFilePath($path);
    }
    private function createManifest($buildId, string $deploymentId): array
    {
        $results = $this->resultRepository->findByBuildIdPendingDeployment($buildId, $deploymentId);
        $manifest = [];
        foreach ($results as $result) {
            if (!$result->size()) {
                continue;
            }
            $manifest[$this->determineFilePath($result->url()->getPath())] = $result;
        }
        return $manifest;
    }
    private function createRemoteDeployment(array $manifest): array
    {
        $files = array_map(function ($result) {
            return $result->sha1();
        }, $manifest);
        return $this->apiRequest("sites/{$this->siteId}/deploys", 'POST', ['json' => ['files' => $files]]);
    }
    private function apiRequest(string $path, string $method = 'GET', array $options = []): array
    {
        $url = sprintf('%s/%s', self::API_URL, $path);
        $options = array_merge_recursive(['headers' => ['Authorization' => 'Bearer ' . $this->accessToken]], $options);
        $response = $this->httpClient->request($method, $url, $options);
        $result = $response->getBody()->getContents();
        return json_decode($result, \true);
    }
    private function getUploadRequests(string $deploymentId, iterable $results): Generator
    {
        foreach ($results as $result) {
            if (!$result->size()) {
                continue;
            }
            $filePath = $this->determineFilePath($result->url()->getPath());
            $resource = $this->resourceRepository->find($result->sha1());
            if ($resource === null) {
                throw new RuntimeException("Unable to find resource for result {$result->sha1()}: is the resource repository configured correctly?");
            }
            yield $result->id() => $this->createUploadRequest($deploymentId, $filePath, $resource->content());
        }
    }
    private function createUploadRequest(string $deploymentId, string $filePath, StreamInterface $content): RequestInterface
    {
        return new Request('PUT', sprintf('%s/deploys/%s/files%s', self::API_URL, $deploymentId, PathEncoder::encode($filePath)), ['Authorization' => 'Bearer ' . $this->accessToken, 'Content-Type' => 'application/octet-stream'], $content);
    }
}
