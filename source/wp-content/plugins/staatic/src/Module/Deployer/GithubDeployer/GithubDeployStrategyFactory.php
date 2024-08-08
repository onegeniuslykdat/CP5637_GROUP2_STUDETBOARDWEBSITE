<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\GithubDeployer;

use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Staatic\Framework\DeployStrategy\GithubDeployStrategy;
use Staatic\Framework\DeployStrategy\DeployStrategyInterface;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\WordPress\Factory\HttpClientFactory;
use Staatic\WordPress\Publication\Publication;

final class GithubDeployStrategyFactory
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
        return new GithubDeployStrategy($this->resultRepository, $this->resourceRepository, $this->httpClientFactory->createClient(
            [
            'should_retry_callback' => [$this, 'shouldRetryCallback']
        ]
        ), $this->options($publication));
    }

    public function shouldRetryCallback(array $options, ?ResponseInterface $response = null): bool
    {
        return (($nullsafeVariable1 = $response) ? $nullsafeVariable1->getReasonPhrase() : null) === 'rate limit exceeded';
    }

    private function options(Publication $publication): array
    {
        $token = get_option(
            'staatic_github_token'
        ) ?: ($_ENV['GH_TOKEN'] ?? $_SERVER['GH_TOKEN'] ?? $_ENV['GITHUB_TOKEN'] ?? $_SERVER['GITHUB_TOKEN'] ?? null);
        if (empty($token)) {
            throw new RuntimeException('GitHub token is not configured.');
        }
        $repositoryPrefix = get_option('staatic_github_prefix') ?: null;
        $commitMessage = get_option('staatic_github_commit_message') ?: null;
        if ($commitMessage) {
            $commitMessage = strtr($commitMessage, [
                '{publicationId}' => $publication->id(),
                '{buildId}' => $publication->build()->id(),
                '{deploymentId}' => $publication->deployment()->id(),
                '{userId}' => $publication->userId() ?: -1
            ]);
        }
        $retainPaths = RetainPaths::resolve(get_option('staatic_github_retain_paths') ?: null, $repositoryPrefix);
        $retainPaths = apply_filters('staatic_github_retain_paths', $retainPaths);

        return [
            'basePath' => $publication->build()->destinationUrl()->getPath(),
            'token' => $token,
            'repository' => get_option('staatic_github_repository'),
            'branch' => get_option('staatic_github_branch'),
            'prefix' => $repositoryPrefix,
            'commitMessage' => $commitMessage,
            'retainPaths' => $retainPaths,
            // Prevents exceeding secondary rate limits.
            'concurrency' => 1,
        ];
    }
}
