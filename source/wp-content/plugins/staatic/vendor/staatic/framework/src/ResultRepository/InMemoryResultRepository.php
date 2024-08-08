<?php

namespace Staatic\Framework\ResultRepository;

use DateTimeImmutable;
use Generator;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use RuntimeException;
use Staatic\Framework\Result;
final class InMemoryResultRepository implements ResultRepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var mixed[]
     */
    private $results = [];
    /**
     * @var mixed[]
     */
    private $deployableResults = [];
    public function __construct()
    {
        $this->logger = new NullLogger();
    }
    public function nextId(): string
    {
        return (string) Uuid::uuid4();
    }
    /**
     * @param Result $result
     */
    public function add($result): void
    {
        $this->logger->debug("Adding result #{$result->id()}", ['resultId' => $result->id()]);
        $this->results[$result->id()] = $result;
    }
    /**
     * @param Result $result
     */
    public function update($result): void
    {
        $this->logger->debug("Updating result #{$result->id()}", ['resultId' => $result->id()]);
        $this->results[$result->id()] = $result;
    }
    /**
     * @param Result $result
     */
    public function delete($result): void
    {
        $this->logger->debug("Deleting result #{$result->id()}", ['resultId' => $result->id()]);
        unset($this->results[$result->id()]);
    }
    /**
     * @param string $sourceBuildId
     * @param string $targetBuildId
     */
    public function mergeBuildResults($sourceBuildId, $targetBuildId): void
    {
        $this->logger->debug("Merging build results from build #{$sourceBuildId} into build #{$targetBuildId}", ['buildId' => $targetBuildId]);
        foreach ($this->results as $sourceResult) {
            if ($sourceResult->buildId() !== $sourceBuildId) {
                continue;
            }
            $targetResult = $this->findOneOrNull(function ($result) use ($targetBuildId, $sourceResult) {
                return $result->buildId() === $targetBuildId && (string) $result->url() === (string) $sourceResult->url();
            });
            if ($targetResult) {
                continue;
            }
            $targetResult = Result::createFromResult($sourceResult, $this->nextId(), $targetBuildId);
            $this->results[$targetResult->id()] = $targetResult;
        }
    }
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function scheduleForDeployment($buildId, $deploymentId): int
    {
        $this->logger->debug("Scheduling results in build #{$buildId} for deployment #{$deploymentId}", ['buildId' => $buildId, 'deploymentId' => $deploymentId]);
        $numResults = 0;
        foreach ($this->results as $result) {
            if ($result->buildId() !== $buildId) {
                continue;
            }
            $this->deployableResults[$result->id()][$deploymentId] = ['dateCreated' => new DateTimeImmutable(), 'dateDeployed' => null];
            $numResults++;
        }
        return $numResults;
    }
    /**
     * @param Result $result
     * @param string $deploymentId
     */
    public function markDeployed($result, $deploymentId): void
    {
        $this->logger->debug("Marking result #{$result->id()} deployed for deployment #{$deploymentId}", ['resultId' => $result->id(), 'deploymentId' => $deploymentId]);
        if (!isset($this->deployableResults[$result->id()][$deploymentId])) {
            throw new RuntimeException("Unable to mark result #{$result->id()} deployed for deployment #{$deploymentId}: unknown result/deployment combination");
        }
        $this->deployableResults[$result->id()][$deploymentId]['dateDeployed'] = new DateTimeImmutable();
    }
    /**
     * @param string $deploymentId
     * @param mixed[] $resultIds
     */
    public function markManyDeployed($deploymentId, $resultIds): void
    {
        $numResults = count($resultIds);
        $this->logger->debug("Marking {$numResults} results deployed for deployment #{$deploymentId}", ['deploymentId' => $deploymentId]);
        foreach ($resultIds as $resultId) {
            if (!isset($this->deployableResults[$resultId][$deploymentId])) {
                throw new RuntimeException("Unable to mark result #{$resultId} deployed for deployment #{$deploymentId}: unknown result/deployment combination");
            }
            $this->deployableResults[$resultId][$deploymentId]['dateDeployed'] = new DateTimeImmutable();
        }
    }
    /**
     * @param string $resultId
     */
    public function find($resultId): ?Result
    {
        return $this->results[$resultId] ?? null;
    }
    public function findAll(): Generator
    {
        foreach ($this->results as $result) {
            yield $result;
        }
    }
    /**
     * @param string $buildId
     */
    public function findByBuildId($buildId): Generator
    {
        foreach ($this->results as $result) {
            if ($result->buildId() === $buildId) {
                yield $result;
            }
        }
    }
    /**
     * @param string $buildId
     */
    public function findByBuildIdWithRedirectUrl($buildId): array
    {
        return array_filter($this->results, function ($result) use ($buildId) {
            return $result->buildId() === $buildId && $result->redirectUrl() !== null;
        });
    }
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function findByBuildIdPendingDeployment($buildId, $deploymentId): Generator
    {
        foreach ($this->deployableResults as $resultId => $deployments) {
            $result = $this->results[$resultId];
            if ($result->buildId() !== $buildId) {
                continue;
            }
            $deployment = $deployments[$deploymentId] ?? null;
            if ($deployment === null || $deployment['dateDeployed'] !== null) {
                continue;
            }
            yield $result;
        }
    }
    /**
     * @param string $buildId
     * @param UriInterface $url
     */
    public function findOneByBuildIdAndUrl($buildId, $url): ?Result
    {
        return $this->findOneOrNull(function ($result) use ($buildId, $url) {
            return $result->buildId() === $buildId && (string) $result->url() === (string) $url;
        });
    }
    /**
     * @param string $buildId
     * @param UriInterface $url
     */
    public function findOneByBuildIdAndUrlResolved($buildId, $url): ?Result
    {
        $result = $this->findOneByBuildIdAndUrl($buildId, $url);
        if (!$result) {
            return null;
        } elseif ($result->statusCodeCategory() === 3) {
            return $this->findOneByBuildIdAndUrlResolved($buildId, $result->redirectUrl());
        } else {
            return $result;
        }
    }
    /**
     * @param string $buildId
     */
    public function countByBuildId($buildId): int
    {
        $generator = $this->findByBuildId($buildId);
        $results = iterator_to_array($generator);
        return count($results);
    }
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function countByBuildIdPendingDeployment($buildId, $deploymentId): int
    {
        $count = 0;
        foreach ($this->deployableResults as $resultId => $deployments) {
            $result = $this->results[$resultId];
            if ($result->buildId() !== $buildId) {
                continue;
            }
            $deployment = $deployments[$deploymentId] ?? null;
            if ($deployment === null || $deployment['dateDeployed'] !== null) {
                continue;
            }
            $count++;
        }
        return $count;
    }
    private function findOneOrNull(callable $callback): ?Result
    {
        foreach ($this->results as $result) {
            if ($callback($result)) {
                return $result;
            }
        }
        return null;
    }
}
