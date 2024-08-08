<?php

declare(strict_types=1);

namespace Staatic\WordPress\Factory;

use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use RuntimeException;
use Staatic\Framework\DeployStrategy\DeployStrategyInterface;
use Staatic\Framework\DeploymentRepository\DeploymentRepositoryInterface;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\Framework\StaticDeployer;
use Staatic\WordPress\Publication\Publication;

final class StaticDeployerFactory
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DeploymentRepositoryInterface
     */
    private $deploymentRepository;

    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;

    /**
     * The number of results to retrieve from the queue per
     * task when there are no strict time limits.
     *
     * @var int
     */
    private const BATCH_SIZE_NORMAL = 96;

    /**
     * The number of results to retrieve from the queue per
     * task when there are strict time limits (e.g. 60 seconds).
     *
     * @var int
     */
    private const BATCH_SIZE_CONSTRAINED = 24;

    public function __construct(LoggerInterface $logger, DeploymentRepositoryInterface $deploymentRepository, ResultRepositoryInterface $resultRepository)
    {
        $this->logger = $logger;
        $this->deploymentRepository = $deploymentRepository;
        $this->resultRepository = $resultRepository;
    }

    public function __invoke(Publication $publication): StaticDeployer
    {
        /** @var DeployStrategyInterface $deployStrategy */
        $deployStrategy = apply_filters('staatic_deployment_strategy', null, $publication);
        if (!$deployStrategy instanceof DeployStrategyInterface) {
            throw new RuntimeException(sprintf(
                'Expected to get a DeployStrategyInterface object, got %s instead',
                is_object($deployStrategy) ? get_class($deployStrategy) : gettype($deployStrategy)
            ));
        }
        if ($deployStrategy instanceof LoggerAwareInterface) {
            $deployStrategy->setLogger($this->logger);
        }

        return new StaticDeployer($this->deploymentRepository, $this->resultRepository, $deployStrategy, $this->logger);
    }

    public function batchSize(bool $limitedResources): int
    {
        $batchSize = $limitedResources ? self::BATCH_SIZE_CONSTRAINED : self::BATCH_SIZE_NORMAL;

        return apply_filters('staatic_deploy_batch_size', $batchSize);
    }
}
