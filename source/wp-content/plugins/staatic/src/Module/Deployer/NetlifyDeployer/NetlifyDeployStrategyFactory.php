<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\NetlifyDeployer;

use Staatic\Framework\DeployStrategy\DeployStrategyInterface;
use Staatic\Framework\DeployStrategy\NetlifyDeployStrategy;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\WordPress\Factory\HttpClientFactory;
use Staatic\WordPress\Publication\Publication;

final class NetlifyDeployStrategyFactory
{
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;

    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    /**
     * @var HttpClientFactory
     */
    private $httpClientFactory;

    public function __construct(ResultRepositoryInterface $resultRepository, ResourceRepositoryInterface $resourceRepository, HttpClientFactory $httpClientFactory)
    {
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
        $this->httpClientFactory = $httpClientFactory;
    }

    public function __invoke(Publication $publication): DeployStrategyInterface
    {
        return new NetlifyDeployStrategy(
            $this->resultRepository,
            $this->resourceRepository,
            $this->httpClientFactory->createClient(),
            $this->options(
            $publication
        )
        );
    }

    private function options(Publication $publication): array
    {
        return [
            'basePath' => $publication->build()->destinationUrl()->getPath(),
            'accessToken' => get_option('staatic_netlify_access_token'),
            'siteId' => get_option('staatic_netlify_site_id'),
            'concurrency' => get_option('staatic_http_concurrency')
        ];
    }
}
