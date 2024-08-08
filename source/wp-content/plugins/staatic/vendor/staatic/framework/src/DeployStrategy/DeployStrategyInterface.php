<?php

namespace Staatic\Framework\DeployStrategy;

use Staatic\Framework\Deployment;
use Staatic\Framework\Result;
interface DeployStrategyInterface
{
    /**
     * @param Deployment $deployment
     */
    public function initiate($deployment): array;
    /**
     * @param Deployment $deployment
     * @param iterable $results
     */
    public function processResults($deployment, $results): void;
    /**
     * @param Deployment $deployment
     */
    public function finish($deployment): bool;
}
