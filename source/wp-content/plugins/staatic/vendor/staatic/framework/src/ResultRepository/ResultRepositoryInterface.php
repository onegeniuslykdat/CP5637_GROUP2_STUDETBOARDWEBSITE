<?php

namespace Staatic\Framework\ResultRepository;

use Generator;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Framework\Result;
interface ResultRepositoryInterface
{
    public function nextId(): string;
    /**
     * @param Result $result
     */
    public function add($result): void;
    /**
     * @param Result $result
     */
    public function update($result): void;
    /**
     * @param Result $result
     */
    public function delete($result): void;
    /**
     * @param string $sourceBuildId
     * @param string $targetBuildId
     */
    public function mergeBuildResults($sourceBuildId, $targetBuildId): void;
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function scheduleForDeployment($buildId, $deploymentId): int;
    /**
     * @param Result $result
     * @param string $deploymentId
     */
    public function markDeployed($result, $deploymentId): void;
    /**
     * @param string $deploymentId
     * @param mixed[] $resultIds
     */
    public function markManyDeployed($deploymentId, $resultIds): void;
    /**
     * @param string $resultId
     */
    public function find($resultId): ?Result;
    public function findAll(): Generator;
    /**
     * @param string $buildId
     */
    public function findByBuildId($buildId): Generator;
    /**
     * @param string $buildId
     */
    public function findByBuildIdWithRedirectUrl($buildId): array;
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function findByBuildIdPendingDeployment($buildId, $deploymentId): Generator;
    /**
     * @param string $buildId
     * @param UriInterface $url
     */
    public function findOneByBuildIdAndUrl($buildId, $url): ?Result;
    /**
     * @param string $buildId
     * @param UriInterface $url
     */
    public function findOneByBuildIdAndUrlResolved($buildId, $url): ?Result;
    /**
     * @param string $buildId
     */
    public function countByBuildId($buildId): int;
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function countByBuildIdPendingDeployment($buildId, $deploymentId): int;
}
