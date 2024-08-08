<?php

namespace Staatic\Framework;

use Generator;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\Framework\DeploymentRepository\DeploymentRepositoryInterface;
use Staatic\Framework\DeployStrategy\DeployStrategyInterface;
class StaticDeployer implements LoggerAwareInterface
{
    /**
     * @var DeploymentRepositoryInterface
     */
    private $deploymentRepository;
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;
    /**
     * @var DeployStrategyInterface
     */
    private $deployStrategy;
    use LoggerAwareTrait;
    private const STATS_UPDATE_FREQUENCY = 12;
    /**
     * @var int|null
     */
    private $numResultsLimit;
    /**
     * @var int|null
     */
    private $numResultsDeployed;
    /**
     * @var int|null
     */
    private $numResultsDeployedNow;
    public function __construct(DeploymentRepositoryInterface $deploymentRepository, ResultRepositoryInterface $resultRepository, DeployStrategyInterface $deployStrategy, ?LoggerInterface $logger = null)
    {
        $this->deploymentRepository = $deploymentRepository;
        $this->resultRepository = $resultRepository;
        $this->deployStrategy = $deployStrategy;
        $this->logger = $logger ?: new NullLogger();
    }
    /**
     * @param Deployment $deployment
     */
    public function initiateDeployment($deployment): void
    {
        $this->logger->notice('Initiating deployment', ['deploymentId' => $deployment->id()]);
        $this->resultRepository->scheduleForDeployment($deployment->buildId(), $deployment->id());
        $deploymentMetadata = $this->deployStrategy->initiate($deployment);
        $numResultsTotal = $this->resultRepository->countByBuildId($deployment->buildId());
        $numResultsDeployable = $this->resultRepository->countByBuildIdPendingDeployment($deployment->buildId(), $deployment->id());
        $deployment->deployStarted($numResultsTotal, $numResultsDeployable, $deploymentMetadata);
        $this->deploymentRepository->update($deployment);
        $this->logger->notice("Deployment initiated ({$numResultsTotal} results total, {$numResultsDeployable} results deployable)", ['deploymentId' => $deployment->id()]);
    }
    /**
     * @param Deployment $deployment
     * @param int|null $numResultsLimit
     */
    public function processResults($deployment, $numResultsLimit = null): bool
    {
        $this->logger->info('Processing results', ['deploymentId' => $deployment->id()]);
        $this->numResultsLimit = $numResultsLimit;
        $this->numResultsDeployed = $deployment->numResultsDeployed();
        $this->numResultsDeployedNow = 0;
        $results = $this->resultsPendingDeployment($deployment->buildId(), $deployment);
        $this->deployStrategy->processResults($deployment, $results);
        if ($this->numResultsDeployedNow > 0) {
            $this->updateDeployStats($deployment, $this->numResultsDeployed);
        }
        $this->logger->info("Finished processing {$this->numResultsDeployed} results", ['deploymentId' => $deployment->id()]);
        return $deployment->numResultsDeployable() <= $deployment->numResultsDeployed();
    }
    private function updateDeployStats(Deployment $deployment, int $numResultsDeployed): void
    {
        $deployment->deployedResults($numResultsDeployed);
        $this->deploymentRepository->update($deployment);
    }
    private function resultsPendingDeployment(string $buildId, Deployment $deployment): Generator
    {
        foreach ($this->resultRepository->findByBuildIdPendingDeployment($buildId, $deployment->id()) as $result) {
            yield $result;
            if (++$this->numResultsDeployed % self::STATS_UPDATE_FREQUENCY === 0) {
                $this->updateDeployStats($deployment, $this->numResultsDeployed);
            }
            $this->resultRepository->markDeployed($result, $deployment->id());
            if ($this->numResultsLimit !== null && ++$this->numResultsDeployedNow >= $this->numResultsLimit) {
                break;
            }
        }
    }
    /**
     * @param Deployment $deployment
     */
    public function finishDeployment($deployment): bool
    {
        $this->logger->info('Waiting for deployment to finish', ['deploymentId' => $deployment->id()]);
        $isFinished = $this->deployStrategy->finish($deployment);
        if ($isFinished) {
            $deployment->deployFinished();
            $this->deploymentRepository->update($deployment);
            $this->logger->notice('Finished deployment', ['deploymentId' => $deployment->id()]);
        }
        return $isFinished;
    }
}
