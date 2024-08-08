<?php

namespace Staatic\Framework\DeployStrategy;

use Exception;
use Staatic\Vendor\GuzzleHttp\Psr7\StreamWrapper;
use InvalidArgumentException;
use Staatic\Vendor\phpseclib3\Crypt\PublicKeyLoader;
use Staatic\Vendor\phpseclib3\Net\SFTP;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
use Staatic\Framework\Deployment;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\Result;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\Framework\Util\PathHelper;
use Staatic\Framework\Util\StreamConverter;
final class SftpDeployStrategy implements DeployStrategyInterface, LoggerAwareInterface
{
    private const META_FILENAME = '.staatic_meta.json';
    use LoggerAwareTrait;
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;
    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;
    /**
     * @var string
     */
    private $basePath;
    /**
     * @var string
     */
    private $host;
    /**
     * @var int
     */
    private $port;
    /**
     * @var string
     */
    private $targetDirectory = '.';
    /**
     * @var string
     */
    private $username;
    /**
     * @var string|null
     */
    private $password;
    /**
     * @var string|null
     */
    private $sshKey;
    /**
     * @var string|null
     */
    private $sshKeyPassword;
    /**
     * @var int
     */
    private $timeout;
    /**
     * @var mixed[]
     */
    private $loggerContext = [];
    /**
     * @var SFTP
     */
    private $sftp;
    public function __construct(ResultRepositoryInterface $resultRepository, ResourceRepositoryInterface $resourceRepository, array $options = [])
    {
        $this->logger = new NullLogger();
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
        if (empty($options['host'])) {
            throw new InvalidArgumentException('Missing required option "host"');
        }
        if (empty($options['username'])) {
            throw new InvalidArgumentException('Missing required option "username"');
        }
        if (empty($options['password']) && empty($options['sshKey'])) {
            throw new InvalidArgumentException('Missing required option "password" or "sshKey"');
        }
        if (!empty($options['targetDirectory']) && $targetDirectory = trim($options['targetDirectory'])) {
            $this->targetDirectory = ($targetDirectory === '/') ? '/' : rtrim($targetDirectory, '/');
        }
        $this->basePath = isset($options['basePath']) ? rtrim($options['basePath'], '/') : '';
        $this->host = $options['host'];
        $this->port = $options['port'] ?? 22;
        $this->username = $options['username'];
        $this->password = $options['password'] ?? null;
        $this->sshKey = $options['sshKey'] ?? null;
        $this->sshKeyPassword = $options['sshKeyPassword'] ?? null;
        $this->timeout = $options['timeout'] ?? 10;
        $this->sftp = new SFTP($this->host, $this->port, $this->timeout);
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
            $localFileHashes[$filePath] = $result->sha1();
            $localFileResultIds[$filePath] = $result->id();
        }
        $this->ensureSftpIsConnected();
        $remoteFiles = $this->listRemoteFiles();
        $remoteFileHashes = [];
        foreach ($remoteFiles as $filePath => $sha1) {
            $remoteFileHashes[$filePath] = $sha1;
        }
        $diff = $this->diffDeploymentFiles($localFileHashes, $remoteFileHashes);
        $metaFile = $this->metaFilePath();
        $this->sftp->put("{$metaFile}.tmp", json_encode($localFileHashes, \JSON_UNESCAPED_SLASHES));
        $resultIds = [];
        foreach ($diff['keep'] as $key => $hash) {
            $resultIds[] = $localFileResultIds[$key];
            if (count($resultIds) >= 100) {
                $this->resultRepository->markManyDeployed($deployment->id(), $resultIds);
                $resultIds = [];
            }
        }
        if (count($resultIds)) {
            $this->resultRepository->markManyDeployed($deployment->id(), $resultIds);
        }
        $this->logger->info(sprintf('Deployment initiated (unmodified files: %d, modified files: "%s", removed files: "%s")', count($diff['keep']), implode('", "', array_keys($diff['upload'])), implode('", "', array_keys($diff['delete']))), $this->loggerContext);
        return ['deleteFiles' => array_keys($diff['delete'])];
    }
    private function listRemoteFiles(): iterable
    {
        $metaFile = $this->metaFilePath();
        if (!$this->sftp->file_exists($metaFile)) {
            $this->logger->notice("Remote file list does not yet exist, assuming new empty target.");
            return [];
        }
        $meta = $this->sftp->get($metaFile);
        return json_decode($meta, \true);
    }
    private function isDotFile(string $path): bool
    {
        return $path === '.' || $path === '..' || substr_compare($path, '/.', -strlen('/.')) === 0 || substr_compare($path, '/..', -strlen('/..')) === 0;
    }
    /**
     * @param Deployment $deployment
     * @param iterable $results
     */
    public function processResults($deployment, $results): void
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $this->logger->info('Deploying results', $this->loggerContext);
        $this->ensureSftpIsConnected();
        foreach ($results as $result) {
            try {
                $filePath = $this->determineFilePath($result->url()->getPath());
                assert(strncmp($filePath, '/', strlen('/')) === 0);
                $resource = $this->resourceRepository->find($result->sha1());
                if ($resource === null) {
                    throw new RuntimeException("Unable to find resource for result {$result->sha1()}: is the resource repository configured correctly?");
                }
                $fileDirectory = dirname($filePath);
                if (!$this->sftp->is_dir($fileDirectory)) {
                    $this->sftp->mkdir($fileDirectory, -1, \true);
                }
                $this->sftp->put($filePath, StreamConverter::streamToResource($resource->content()));
            } catch (Exception $e) {
                $this->logger->error("Deployment of result #{$result->id()} failed: {$e->getMessage()}", array_merge($this->loggerContext, ['resultId' => $result->id()]));
                continue;
            }
            $this->logger->info("Deployment of result #{$result->id()} was successful", array_merge($this->loggerContext, ['resultId' => $result->id()]));
        }
    }
    /**
     * @param Deployment $deployment
     */
    public function finish($deployment): bool
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $this->logger->info('Finishing deployment', $this->loggerContext);
        $this->ensureSftpIsConnected();
        if ($deployment->metadata()) {
            $this->deleteStaleFiles($deployment->metadata());
        }
        $this->swapMetaFile();
        return \true;
    }
    private function deleteStaleFiles(array $metadata): void
    {
        foreach ($metadata['deleteFiles'] ?? [] as $filePath) {
            $this->sftp->delete($filePath);
        }
    }
    private function swapMetaFile(): void
    {
        $metaFile = $this->metaFilePath();
        $this->sftp->rename($metaFile, "{$metaFile}.old");
        $this->sftp->rename("{$metaFile}.tmp", $metaFile);
        $this->sftp->delete("{$metaFile}.old");
    }
    private function determineFilePath(string $path): string
    {
        if ($this->basePath && strncmp($path, $this->basePath, strlen($this->basePath)) === 0) {
            $path = mb_substr($path, mb_strlen($this->basePath));
        }
        return $this->targetDirectory . PathHelper::determineFilePath($path);
    }
    private function diffDeploymentFiles(array $localFiles, array $remoteFiles): array
    {
        return ['keep' => array_intersect_assoc($localFiles, $remoteFiles), 'upload' => array_diff_assoc($localFiles, $remoteFiles), 'delete' => array_diff_key($remoteFiles, $localFiles)];
    }
    private function metaFilePath(): string
    {
        return sprintf('%s/%s', $this->targetDirectory, self::META_FILENAME);
    }
    private function ensureSftpIsConnected(): void
    {
        if ($this->sftp->isConnected()) {
            return;
        }
        $this->sftp->login($this->username, $this->sshKey ? PublicKeyLoader::load($this->sshKey, $this->sshKeyPassword ?: \false) : $this->password);
    }
}
