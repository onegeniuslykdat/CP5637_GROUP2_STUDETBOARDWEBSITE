<?php

namespace Staatic\Framework\DeployStrategy;

use Generator;
use Staatic\Vendor\GuzzleHttp\ClientInterface;
use Staatic\Vendor\GuzzleHttp\Exception\ClientException;
use Staatic\Vendor\GuzzleHttp\Exception\RequestException;
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
use Staatic\Framework\Util\PathHelper;
use Throwable;
final class GithubDeployStrategy implements DeployStrategyInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    private const API_URL = 'https://api.github.com';
    private const API_VERSION = '2022-11-28';
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
    private $basePath;
    /**
     * @var string
     */
    private $repository;
    /**
     * @var string
     */
    private $branch;
    /**
     * @var string
     */
    private $prefix = '';
    /**
     * @var string
     */
    private $commitMessage;
    /**
     * @var string
     */
    private $token;
    /**
     * @var int
     */
    private $concurrency;
    /**
     * @var mixed[]
     */
    private $retainPaths = [];
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
        if (empty($options['repository'])) {
            throw new InvalidArgumentException('Missing required option "repository"');
        }
        if (empty($options['token'])) {
            throw new InvalidArgumentException('Missing required option "token"');
        }
        if (!empty($options['prefix']) && $prefix = trim($options['prefix'])) {
            $this->prefix = $prefix . '/';
        }
        if (!empty($options['retainPaths'])) {
            $this->retainPaths = $options['retainPaths'];
        }
        $this->basePath = isset($options['basePath']) ? rtrim($options['basePath'], '/') : '';
        $this->repository = $options['repository'];
        $this->branch = $options['branch'] ?? 'main';
        $this->commitMessage = $options['commitMessage'] ?? 'Staatic deployment {shortId}';
        $this->token = $options['token'];
        $this->concurrency = $options['concurrency'] ?? 4;
    }
    /**
     * @param Deployment $deployment
     */
    public function initiate($deployment): array
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $localFileHashes = [];
        $localFileResultIds = [];
        $results = $this->resultRepository->findByBuildIdPendingDeployment($deployment->buildId(), $deployment->id());
        foreach ($results as $result) {
            $filePath = $this->determineFilePath($result->url()->getPath());
            $resource = $this->resourceRepository->find($result->sha1());
            if ($resource === null) {
                throw new RuntimeException("Unable to find resource for '{$result->url()->getPath()}' with hash {$result->sha1()}: is the resource repository configured correctly?");
            }
            $header = sprintf("blob %d\x00", $resource->size());
            $content = $resource->content()->getContents();
            $localFileHashes[$filePath] = sha1($header . $content);
            $localFileResultIds[$filePath] = $result->id();
        }
        $latestCommitSha = null;
        $baseTreeSha = null;
        try {
            $response = $this->apiRequest("repos/{$this->repository}/git/ref/heads/{$this->branch}");
            $latestCommitSha = $response['object']['sha'];
        } catch (ClientException $e) {
            if (strpos($e->getMessage(), 'Git Repository is empty.') !== false) {
                $this->logger->warning('Repository empty, initializing repository', $this->loggerContext);
                [$latestCommitSha, $baseTreeSha] = $this->initializeRepository();
            } elseif ($e->getCode() === 404) {
                throw new RuntimeException("Repository or branch does not exist: {$this->repository}/{$this->branch}", 404, $e);
            } else {
                throw $e;
            }
        }
        if ($baseTreeSha === null) {
            $response = $this->apiRequest("repos/{$this->repository}/git/commits/{$latestCommitSha}");
            $baseTreeSha = $response['tree']['sha'];
        }
        $objects = $this->listGithubFiles();
        $remoteFileHashes = [];
        foreach ($objects as $object) {
            $remoteFileHashes[$object['path']] = $object['sha'];
        }
        $diff = $this->diffDeploymentFiles($localFileHashes, $remoteFileHashes);
        foreach ($this->retainPaths as $retainPath) {
            $diff['delete'] = array_filter($diff['delete'], function ($path) use ($retainPath) {
                return strncmp($path, $retainPath, strlen($retainPath)) !== 0;
            }, \ARRAY_FILTER_USE_KEY);
        }
        $resultIds = [];
        foreach ($diff['keep'] as $filePath => $hash) {
            $resultIds[] = $localFileResultIds[$filePath];
            if (count($resultIds) >= 100) {
                $this->resultRepository->markManyDeployed($deployment->id(), $resultIds);
                $resultIds = [];
            }
        }
        if (count($resultIds)) {
            $this->resultRepository->markManyDeployed($deployment->id(), $resultIds);
        }
        $this->logger->info(sprintf('Deployment initiated (unmodified files: %d, modified files: "%s", removed files: "%s")', count($diff['keep']), implode('", "', array_keys($diff['upload'])), implode('", "', array_keys($diff['delete']))), $this->loggerContext);
        if (empty($diff['upload']) && empty($diff['upload']) && empty($diff['delete'])) {
            return [];
        }
        return ['uploadFiles' => $diff['upload'], 'deleteFiles' => $diff['delete'], 'latestCommitSha' => $latestCommitSha, 'baseTreeSha' => $baseTreeSha];
    }
    private function initializeRepository(): array
    {
        $readmeContent = "# New Repository\nThis is a temporary placeholder.";
        $response = $this->apiRequest("repos/{$this->repository}/contents/README.md", 'PUT', ['json' => ['message' => 'Initial commit', 'content' => base64_encode($readmeContent), 'branch' => $this->branch]]);
        return [$response['commit']['sha'], $response['commit']['tree']['sha']];
    }
    /**
     * @param Deployment $deployment
     * @param iterable $results
     */
    public function processResults($deployment, $results): void
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $this->logger->info('Deploying results', $this->loggerContext);
        $pool = new Pool($this->httpClient, $this->getUploadRequests($results), ['concurrency' => $this->concurrency, 'fulfilled' => function (ResponseInterface $response, string $resultId) {
            $this->logger->info("Deployment of result #{$resultId} was successful", array_merge($this->loggerContext, ['resultId' => $resultId]));
        }, 'rejected' => function (Throwable $e, string $resultId) {
            if ($e instanceof RequestException && (($nullsafeVariable1 = $e->getResponse()) ? $nullsafeVariable1->getReasonPhrase() : null) === 'rate limit exceeded') {
                throw new RuntimeException("Rate limit exceeded during deployment of #{$resultId}; consider committing changes manually.");
            }
            $this->logger->error("Deployment of result #{$resultId} failed: {$e->getMessage()}", array_merge($this->loggerContext, ['resultId' => $resultId]));
        }]);
        $promise = $pool->promise();
        $promise->wait();
    }
    /**
     * @param Deployment $deployment
     */
    public function finish($deployment): bool
    {
        $metadata = $deployment->metadata();
        if (empty($metadata['baseTreeSha'])) {
            return \true;
        }
        $tree = [];
        foreach ($metadata['uploadFiles'] as $path => $sha) {
            $tree[] = ['path' => $path, 'mode' => '100644', 'type' => 'blob', 'sha' => $sha];
        }
        foreach ($metadata['deleteFiles'] as $path => $sha) {
            $tree[] = ['path' => $path, 'mode' => '100644', 'type' => 'blob', 'sha' => null];
        }
        $response = $this->apiRequest("repos/{$this->repository}/git/trees", 'POST', ['json' => ['base_tree' => $metadata['baseTreeSha'], 'tree' => $tree]]);
        $treeSha = $response['sha'];
        if ($treeSha === $metadata['baseTreeSha']) {
            $this->logger->warning('New tree did not result in changes', $this->loggerContext);
            return \true;
        }
        $response = $this->apiRequest("repos/{$this->repository}/git/commits", 'POST', ['json' => ['message' => strtr($this->commitMessage, ['{id}' => $deployment->id(), '{shortId}' => substr($deployment->id(), strrpos($deployment->id(), '-') + 1)]), 'parents' => [$metadata['latestCommitSha']], 'tree' => $treeSha]]);
        $commitSha = $response['sha'];
        $response = $this->apiRequest("repos/{$this->repository}/git/refs/heads/{$this->branch}", 'PATCH', ['json' => ['sha' => $commitSha, 'force' => \true]]);
        return \true;
    }
    private function determineFilePath(string $path): string
    {
        if ($this->basePath && strncmp($path, $this->basePath, strlen($this->basePath)) === 0) {
            $path = mb_substr($path, mb_strlen($this->basePath));
        }
        return $this->prefix . substr(PathHelper::determineFilePath($path), 1);
    }
    private function diffDeploymentFiles(array $localFiles, array $remoteFiles): array
    {
        return ['keep' => array_intersect_assoc($localFiles, $remoteFiles), 'upload' => array_diff_assoc($localFiles, $remoteFiles), 'delete' => array_diff_key($remoteFiles, $localFiles)];
    }
    private function listGithubFiles(): array
    {
        $response = $this->apiRequest("repos/{$this->repository}/git/trees/{$this->branch}", 'GET', ['query' => ['recursive' => '1']]);
        if (!isset($response['tree']) || !is_array($response['tree'])) {
            throw new RuntimeException("Unable to list files from GitHub: missing tree");
        }
        if ($response['truncated']) {
            throw new RuntimeException("Unable to list files from GitHub: repository too large");
        }
        $result = [];
        foreach ($response['tree'] as $object) {
            if ($object['type'] !== 'blob') {
                continue;
            }
            if ($this->prefix && strncmp($object['path'], $this->prefix, strlen($this->prefix)) !== 0) {
                continue;
            }
            $result[] = $object;
        }
        return $result;
    }
    private function apiRequest(string $path, string $method = 'GET', array $options = []): array
    {
        $url = sprintf('%s/%s', self::API_URL, $path);
        $options = array_merge_recursive(['headers' => ['Accept' => 'application/vnd.github+json', 'Authorization' => 'token ' . $this->token, 'X-GitHub-Api-Version' => self::API_VERSION]], $options);
        $response = $this->httpClient->request($method, $url, $options);
        $body = $response->getBody()->getContents();
        return json_decode($body, \true);
    }
    private function getUploadRequests(iterable $results): Generator
    {
        foreach ($results as $result) {
            $resource = $this->resourceRepository->find($result->sha1());
            if ($resource === null) {
                throw new RuntimeException("Unable to find resource for result {$result->sha1()}: is the resource repository configured correctly?");
            }
            yield $result->id() => $this->createUploadRequest($resource->content());
        }
    }
    private function createUploadRequest(StreamInterface $content): RequestInterface
    {
        return new Request('POST', self::API_URL . "/repos/{$this->repository}/git/blobs", ['Accept' => 'application/vnd.github+json', 'Authorization' => 'token ' . $this->token, 'X-GitHub-Api-Version' => self::API_VERSION], json_encode(['content' => base64_encode($content->getContents()), 'encoding' => 'base64'], \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE));
    }
}
