<?php

namespace Staatic\Framework\PostProcessor;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\UriResolver;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Crawler\CrawlProfile\CrawlProfileInterface;
use Staatic\Framework\Build;
use Staatic\Framework\Resource;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\Result;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\Framework\Transformer\TransformerCollection;
final class AdditionalRedirectsPostProcessor implements PostProcessorInterface, LoggerAwareInterface
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
     * @var iterable
     */
    private $additionalRedirects;
    /**
     * @var CrawlProfileInterface
     */
    private $crawlProfile;
    use LoggerAwareTrait;
    /**
     * @var TransformerCollection
     */
    private $transformers;
    /**
     * @var string
     */
    private $template;
    /**
     * @var Build
     */
    private $build;
    public function __construct(ResultRepositoryInterface $resultRepository, ResourceRepositoryInterface $resourceRepository, iterable $additionalRedirects, CrawlProfileInterface $crawlProfile, ?TransformerCollection $transformers = null, ?string $template = null)
    {
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
        $this->additionalRedirects = $additionalRedirects;
        $this->crawlProfile = $crawlProfile;
        $this->logger = new NullLogger();
        $this->transformers = $transformers ?: new TransformerCollection();
        $this->template = $template ?: $this->defaultTemplate();
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
        $this->logger->info("Applying additional redirects post processor", ['buildId' => $build->id()]);
        $this->build = $build;
        $numApplied = 0;
        foreach ($this->additionalRedirects as $additionalRedirect) {
            $this->createRedirectResult($additionalRedirect->origin(), $additionalRedirect->redirectUrl(), $additionalRedirect->statusCode());
            $numApplied++;
        }
        $this->logger->info("Applied additional redirects post processor ({$numApplied} redirects)", ['buildId' => $build->id()]);
    }
    private function createRedirectResult(string $origin, UriInterface $redirectUrl, int $statusCode): void
    {
        $resultUrl = $this->determineResultUrl($origin);
        $existingResult = $this->resultRepository->findOneByBuildIdAndUrl($this->build->id(), $resultUrl);
        if ($existingResult) {
            $this->logger->warning("Skipping additional redirect with URL '{$resultUrl}'; a result with the same URL already exists", ['buildId' => $this->build->id()]);
            return;
        }
        $redirectUrl = $this->determineRedirectUrl($redirectUrl, $resultUrl);
        $this->logger->debug("Adding result for redirect with URL '{$resultUrl}', redirecting to '{$redirectUrl}'", ['buildId' => $this->build->id()]);
        $resource = Resource::create(sprintf($this->template, $redirectUrl));
        $result = Result::create($this->resultRepository->nextId(), $this->build->id(), $resultUrl, md5((string) $resultUrl), $resource, ['statusCode' => $statusCode, 'redirectUrl' => $redirectUrl]);
        $this->transformers->apply($result, $resource);
        $result->syncResource($resource);
        $this->resourceRepository->write($resource);
        $this->resultRepository->add($result);
    }
    private function determineResultUrl(string $origin): UriInterface
    {
        return $this->crawlProfile->transformUrl(new Uri($origin))->transformedUrl();
    }
    private function determineRedirectUrl(UriInterface $redirectUrl, UriInterface $baseUrl): UriInterface
    {
        $resolvedBaseUrl = UriResolver::resolve($this->crawlProfile->baseUrl(), $baseUrl);
        $resolvedRedirectUrl = UriResolver::resolve($resolvedBaseUrl, $redirectUrl);
        if (!$this->crawlProfile->shouldCrawl($resolvedRedirectUrl)) {
            return $redirectUrl;
        }
        return $this->crawlProfile->transformUrl($redirectUrl, $resolvedBaseUrl)->effectiveUrl();
    }
    private function defaultTemplate(): string
    {
        return <<<EOT
<html>
    <head>
        <title>Redirecting</title>
        <meta http-equiv="refresh" content="0;url=%s" />
    </head>
    <body></body>
</html>
EOT;
    }
}
