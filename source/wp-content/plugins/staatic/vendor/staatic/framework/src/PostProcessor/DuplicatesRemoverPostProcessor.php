<?php

namespace Staatic\Framework\PostProcessor;

use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Framework\Build;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
final class DuplicatesRemoverPostProcessor implements PostProcessorInterface, LoggerAwareInterface
{
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;
    use LoggerAwareTrait;
    public function __construct(ResultRepositoryInterface $resultRepository)
    {
        $this->resultRepository = $resultRepository;
        $this->logger = new NullLogger();
    }
    public function createsOrRemovesResults(): bool
    {
        return \true;
    }
    /**
     * @param Build $build
     */
    public function apply($build): void
    {
        $this->logger->info('Applying duplicates remover post processor', ['buildId' => $build->id()]);
        $numDeleted = 0;
        foreach ($this->resultRepository->findByBuildIdWithRedirectUrl($build->id()) as $result) {
            if ($result->url()->getAuthority() !== $result->redirectUrl()->getAuthority()) {
                continue;
            }
            $path = $result->url()->getPath();
            $comparePath = substr_compare($path, '/', -strlen('/')) === 0 ? rtrim($path, '/') : sprintf('%s/', $path);
            if ($result->redirectUrl()->getPath() !== $comparePath) {
                continue;
            }
            $this->logger->debug("Deleting unprocessable result with url '{$result->url()}' (redirects to '{$result->redirectUrl()}')", ['buildId' => $build->id(), 'resultId' => $result->id()]);
            $this->resultRepository->delete($result);
            $numDeleted++;
        }
        $this->logger->info("Removed {$numDeleted} duplicates", ['buildId' => $build->id()]);
    }
}
