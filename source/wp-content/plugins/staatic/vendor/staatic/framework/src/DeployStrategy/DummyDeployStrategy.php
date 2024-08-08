<?php

namespace Staatic\Framework\DeployStrategy;

use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Framework\Deployment;
final class DummyDeployStrategy implements DeployStrategyInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    public function __construct()
    {
        $this->logger = new NullLogger();
    }
    /**
     * @param Deployment $deployment
     */
    public function initiate($deployment): array
    {
        $this->logger->info("Initiating dummy deployment");
        return [];
    }
    /**
     * @param Deployment $deployment
     * @param iterable $results
     */
    public function processResults($deployment, $results): void
    {
        $numResults = 0;
        foreach ($results as $result) {
            $numResults++;
        }
        $this->logger->info("Deployed {$numResults} files");
    }
    /**
     * @param Deployment $deployment
     */
    public function finish($deployment): bool
    {
        $this->logger->info("Finishing dummy deployment");
        return \true;
    }
}
