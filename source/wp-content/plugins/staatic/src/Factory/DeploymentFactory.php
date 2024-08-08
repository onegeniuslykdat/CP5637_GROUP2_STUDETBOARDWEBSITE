<?php

declare(strict_types=1);

namespace Staatic\WordPress\Factory;

use Staatic\Framework\Deployment;
use Staatic\Framework\DeploymentRepository\DeploymentRepositoryInterface;

final class DeploymentFactory
{
    /**
     * @var DeploymentRepositoryInterface
     */
    private $deploymentRepository;

    public function __construct(DeploymentRepositoryInterface $deploymentRepository)
    {
        $this->deploymentRepository = $deploymentRepository;
    }

    public function create(string $buildId): Deployment
    {
        $deployment = new Deployment($this->deploymentRepository->nextId(), $buildId);
        $this->deploymentRepository->add($deployment);

        return $deployment;
    }
}
