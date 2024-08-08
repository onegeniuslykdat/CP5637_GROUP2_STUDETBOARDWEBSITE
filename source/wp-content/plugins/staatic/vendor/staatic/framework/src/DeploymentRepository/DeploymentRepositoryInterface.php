<?php

namespace Staatic\Framework\DeploymentRepository;

use Staatic\Framework\Deployment;
interface DeploymentRepositoryInterface
{
    public function nextId(): string;
    /**
     * @param Deployment $deployment
     */
    public function add($deployment): void;
    /**
     * @param Deployment $deployment
     */
    public function update($deployment): void;
    /**
     * @param string $deploymentId
     */
    public function find($deploymentId): ?Deployment;
}
