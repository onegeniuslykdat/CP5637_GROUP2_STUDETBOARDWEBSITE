<?php

namespace Staatic\Crawler;

use Staatic\Crawler\Event\StartsCrawling;
use Staatic\Crawler\Event\FinishedCrawling;
use Staatic\Crawler\Event\CrawlRequestFulfilled;
use Staatic\Crawler\Event\CrawlRequestRejected;
use Generator;
use Staatic\Vendor\GuzzleHttp\Exception\RequestException;
use Staatic\Vendor\GuzzleHttp\ClientInterface;
use Staatic\Vendor\GuzzleHttp\Pool;
use Staatic\Vendor\GuzzleHttp\Psr7\Request;
use Staatic\Vendor\GuzzleHttp\RequestOptions;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
use SplObjectStorage;
use SplObserver;
use Staatic\Crawler\CrawlOptions;
use Staatic\Crawler\CrawlProfile\CrawlProfileInterface;
use Staatic\Crawler\CrawlQueue\CrawlQueueInterface;
use Staatic\Crawler\CrawlUrlProvider\CrawlUrlProviderCollection;
use Staatic\Crawler\CrawlUrlProvider\CrawlUrlProviderInterface;
use Staatic\Crawler\Event;
use Staatic\Crawler\Event\EventInterface;
use Staatic\Crawler\KnownUrlsContainer\KnownUrlsContainerInterface;
use Staatic\Crawler\ResponseHandler\ResponseHandlerInterface;
use Staatic\Crawler\UrlTransformer\UrlTransformation;
use Throwable;
final class Crawler implements CrawlerInterface, LoggerAwareInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;
    /**
     * @var CrawlProfileInterface
     */
    private $crawlProfile;
    /**
     * @var CrawlQueueInterface
     */
    private $crawlQueue;
    /**
     * @var KnownUrlsContainerInterface
     */
    private $knownUrlsContainer;
    /**
     * @var CrawlOptions
     */
    private $crawlOptions;
    use LoggerAwareTrait;
    /**
     * @var SplObjectStorage
     */
    private $observers;
    /**
     * @var EventInterface|null
     */
    private $event;
    /**
     * @var ResponseHandlerInterface
     */
    private $responseFulfilledHandlerChain;
    /**
     * @var ResponseHandlerInterface
     */
    private $responseRejectedHandlerChain;
    /**
     * @var mixed[]
     */
    private $pendingCrawlsById = [];
    /**
     * @var int
     */
    private $numCrawlProcessed = 0;
    public function __construct(ClientInterface $httpClient, CrawlProfileInterface $crawlProfile, CrawlQueueInterface $crawlQueue, KnownUrlsContainerInterface $knownUrlsContainer, CrawlOptions $crawlOptions)
    {
        $this->httpClient = $httpClient;
        $this->crawlProfile = $crawlProfile;
        $this->crawlQueue = $crawlQueue;
        $this->knownUrlsContainer = $knownUrlsContainer;
        $this->crawlOptions = $crawlOptions;
        $this->logger = new NullLogger();
        $this->observers = new SplObjectStorage();
    }
    /**
     * @param CrawlUrlProviderCollection $crawlUrlProviders
     */
    public function initialize($crawlUrlProviders): int
    {
        $this->crawlQueue->clear();
        $this->knownUrlsContainer->clear();
        $totalEnqueued = 0;
        foreach ($crawlUrlProviders as $crawlUrlProvider) {
            $this->logger->info(sprintf("%s: enqueueing crawl URLs", self::shortClassName($crawlUrlProvider)));
            $numEnqueued = $this->enqueueProvidedCrawlUrls($crawlUrlProvider);
            $this->logger->info(sprintf("%s: %d crawl URLs enqueued", self::shortClassName($crawlUrlProvider), $numEnqueued));
            $totalEnqueued += $numEnqueued;
        }
        return $totalEnqueued;
    }
    private function enqueueProvidedCrawlUrls(CrawlUrlProviderInterface $crawlUrlProvider): int
    {
        $numEnqueued = 0;
        $numMore = 0;
        foreach ($crawlUrlProvider->provide() as $crawlUrl) {
            if (!$this->shouldCrawl($crawlUrl->url())) {
                continue;
            }
            if (!$crawlUrl->transformedUrl()) {
                $transformedUrl = $this->transformUrl($crawlUrl->url(), $crawlUrl->foundOnUrl())->transformedUrl();
                $crawlUrl = $crawlUrl->withTransformedUrl($transformedUrl);
            }
            $crawlUrl = $crawlUrl->withTag(self::TAG_PROVIDED_URL);
            if ($numEnqueued < 10) {
                $this->logger->debug(sprintf("%s: enqueueing '%s'", self::shortClassName($crawlUrlProvider), $crawlUrl->url()));
            } elseif (++$numMore >= 50) {
                $this->logger->debug(sprintf("%s: enqueued %d more URLs...", self::shortClassName($crawlUrlProvider), $numMore));
                $numMore = 0;
            }
            $this->addToCrawlQueue($crawlUrl);
            $numEnqueued++;
        }
        if ($numMore) {
            $this->logger->debug(sprintf("%s: enqueued %d more URLs...", self::shortClassName($crawlUrlProvider), $numMore));
        }
        return $numEnqueued;
    }
    public function crawl(): int
    {
        $this->notifyStartsCrawling();
        foreach ($this->crawlOptions->responseFulfilledHandlers() as $responseHandler) {
            if ($responseHandler instanceof LoggerAwareInterface) {
                $responseHandler->setLogger($this->logger);
            }
        }
        $this->responseFulfilledHandlerChain = $this->crawlOptions->responseFulfilledHandlers()->toChain($this);
        foreach ($this->crawlOptions->responseRejectedHandlers() as $responseHandler) {
            if ($responseHandler instanceof LoggerAwareInterface) {
                $responseHandler->setLogger($this->logger);
            }
        }
        $this->responseRejectedHandlerChain = $this->crawlOptions->responseRejectedHandlers()->toChain($this);
        $this->numCrawlProcessed = 0;
        $this->crawlLoop();
        if ($this->isFinishedCrawling()) {
            $this->notifyFinishedCrawling();
        }
        return $this->numCrawlProcessed;
    }
    private function crawlLoop(): void
    {
        while (!$this->isFinishedCrawling() && !$this->maxCrawlsReached()) {
            $this->startCrawlQueue();
        }
    }
    private function maxCrawlsReached(): bool
    {
        $maxCrawls = $this->crawlOptions->maxCrawls();
        return $maxCrawls !== null && $this->numCrawlProcessed >= $maxCrawls;
    }
    private function isFinishedCrawling(): bool
    {
        return count($this->crawlQueue) === 0;
    }
    private function notifyStartsCrawling()
    {
        $this->setEvent(new StartsCrawling());
        $this->notify();
    }
    private function notifyFinishedCrawling()
    {
        $this->setEvent(new FinishedCrawling());
        $this->notify();
    }
    /**
     * @param UriInterface $resolvedUrl
     * @param mixed[] $context
     */
    public function shouldCrawl($resolvedUrl, $context = []): bool
    {
        if (!$this->hasCrawlableScheme($resolvedUrl)) {
            return \false;
        }
        if (!$this->crawlProfile->shouldCrawl($resolvedUrl, $context)) {
            return \false;
        }
        return \true;
    }
    private function hasCrawlableScheme(UriInterface $url): bool
    {
        return in_array($url->getScheme(), ['http', 'https'], \true);
    }
    /**
     * @param CrawlUrl $crawlUrl
     */
    public function addToCrawlQueue($crawlUrl): void
    {
        $normalizedUrl = $this->crawlProfile->normalizeUrl($crawlUrl->transformedUrl());
        if ($this->knownUrlsContainer->isKnown($normalizedUrl)) {
            return;
        }
        $this->knownUrlsContainer->add($normalizedUrl);
        $crawlUrl = $crawlUrl->withNormalizedUrl($normalizedUrl);
        $maxDepth = $this->shouldIgnoreMaxDepth($crawlUrl) ? null : $this->crawlOptions->maxDepth();
        if ($maxDepth !== null && $crawlUrl->depthLevel() >= $maxDepth) {
            return;
        }
        $priority = $this->determineCrawlUrlPriority($crawlUrl);
        $this->crawlQueue->enqueue($crawlUrl, $priority);
    }
    private function shouldIgnoreMaxDepth(CrawlUrl $crawlUrl): bool
    {
        if ($crawlUrl->hasTag(self::TAG_PROVIDED_URL)) {
            return \true;
        }
        if ($crawlUrl->hasTag(self::TAG_SITEMAP_XML)) {
            return \true;
        }
        if ($this->crawlOptions->forceAssets() && $this->determineIsAsset($crawlUrl)) {
            return \true;
        }
        return \false;
    }
    private function determineIsAsset(CrawlUrl $crawlUrl): bool
    {
        return preg_match($this->crawlOptions->assetsPattern(), $crawlUrl->url()->getPath()) === 1;
    }
    private function determineCrawlUrlPriority(CrawlUrl $crawlUrl): int
    {
        switch (\true) {
            case $crawlUrl->hasTag(self::TAG_PRIORITY_HIGH):
                return 90;
            case $crawlUrl->hasTag(self::TAG_PRIORITY_LOW):
                return 30;
            default:
                return 60;
        }
    }
    private function startCrawlQueue(): void
    {
        $pool = new Pool($this->httpClient, $this->getHttpRequests(), ['concurrency' => $this->crawlOptions->concurrency(), 'fulfilled' => function (ResponseInterface $response, $index) {
            $this->handleRequestFulfilled($response, $index);
        }, 'rejected' => function (Throwable $transferException, $index) {
            $this->handleRequestRejected($transferException, $index);
        }, 'options' => [RequestOptions::ALLOW_REDIRECTS => \false]]);
        $promise = $pool->promise();
        $promise->wait();
    }
    private function getHttpRequests(): Generator
    {
        while ($this->crawlQueue->count() && !$this->maxCrawlsReached()) {
            $crawlUrl = $this->crawlQueue->dequeue();
            $this->pendingCrawlsById[$crawlUrl->id()] = PendingCrawl::create($crawlUrl);
            $this->numCrawlProcessed++;
            $this->logger->debug("Preparing request for '{$crawlUrl->url()}'", ['crawlUrlId' => $crawlUrl->id()]);
            yield $crawlUrl->id() => new Request('GET', $crawlUrl->url());
        }
    }
    private function handleRequestFulfilled(ResponseInterface $response, string $crawlUrlId): void
    {
        $pendingCrawl = $this->pendingCrawlsById[$crawlUrlId]->withEndTime();
        unset($this->pendingCrawlsById[$crawlUrlId]);
        $crawlUrl = $pendingCrawl->crawlUrl()->withResponse($response);
        $this->logger->debug("Fulfilled request for '{$crawlUrl->url()}'", ['crawlUrlId' => $crawlUrl->id(), 'timeTaken' => $pendingCrawl->timeTaken()]);
        if (!$crawlUrl->hasTag(self::TAG_DONT_TOUCH)) {
            $crawlUrl = $this->responseFulfilledHandlerChain->handle($crawlUrl);
        }
        $this->notifyCrawlRequestFulfilled($crawlUrl);
    }
    private function notifyCrawlRequestFulfilled(CrawlUrl $crawlUrl): void
    {
        $this->setEvent(CrawlRequestFulfilled::create($crawlUrl));
        $this->notify();
    }
    private function handleRequestRejected(Throwable $transferException, string $crawlUrlId): void
    {
        $pendingCrawl = $this->pendingCrawlsById[$crawlUrlId]->withEndTime();
        unset($this->pendingCrawlsById[$crawlUrlId]);
        $crawlUrl = $pendingCrawl->crawlUrl();
        if ($transferException instanceof RequestException) {
            $crawlUrl = $crawlUrl->withResponse($transferException->getResponse());
        }
        $this->logger->debug("Rejected request for '{$crawlUrl->url()}'", ['crawlUrlId' => $crawlUrl->id(), 'timeTaken' => $pendingCrawl->timeTaken()]);
        if ($crawlUrl->hasTag(self::TAG_ENTRY_URL)) {
            throw new RuntimeException("Crawling entry URL '{$crawlUrl->url()}' failed: {$transferException->getMessage()}");
        }
        if (!$this->shouldProcessNotFoundResponse($crawlUrl, $transferException)) {
            $crawlUrl = $crawlUrl->withTags(array_merge($crawlUrl->tags(), [self::TAG_DONT_SAVE]));
        } elseif (!$crawlUrl->hasTag(self::TAG_DONT_TOUCH)) {
            $crawlUrl = $this->responseRejectedHandlerChain->handle($crawlUrl);
        }
        $this->notifyCrawlRequestRejected($crawlUrl, $transferException);
    }
    private function shouldProcessNotFoundResponse(CrawlUrl $crawlUrl, Throwable $transferException): bool
    {
        if ($this->crawlOptions()->processNotFound()) {
            return \true;
        }
        if ($transferException->getCode() !== 404) {
            return \true;
        }
        return $crawlUrl->hasTag(self::TAG_PAGE_NOT_FOUND);
    }
    private function notifyCrawlRequestRejected(CrawlUrl $crawlUrl, Throwable $transferException): void
    {
        $this->setEvent(CrawlRequestRejected::create($crawlUrl, $transferException));
        $this->notify();
    }
    /**
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $context
     */
    public function transformUrl($url, $foundOnUrl = null, $context = []): UrlTransformation
    {
        return $this->crawlProfile->transformUrl($url, $foundOnUrl, $context);
    }
    public function crawlOptions(): CrawlOptions
    {
        return $this->crawlOptions;
    }
    public function numUrlsCrawlable(): int
    {
        return $this->knownUrlsContainer->count();
    }
    public function attach(SplObserver $observer): void
    {
        $this->logger->debug(sprintf('Attaching observer \'%s\'', self::shortClassName($observer)));
        $this->observers->attach($observer);
    }
    public function detach(SplObserver $observer): void
    {
        $this->logger->debug(sprintf('Detaching observer \'%s\'', self::shortClassName($observer)));
        $this->observers->detach($observer);
    }
    public function notify(): void
    {
        $this->logger->debug(sprintf('Notifying %d observers about \'%s\'', count($this->observers), self::shortClassName($this->event)));
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
    public function getEvent(): ?EventInterface
    {
        return $this->event;
    }
    /**
     * @param EventInterface $event
     */
    public function setEvent($event): void
    {
        $this->event = $event;
    }
    /**
     * @param string|object $class
     */
    private static function shortClassName($class): string
    {
        $className = is_string($class) ? $class : get_class($class);
        return substr($className, strrpos($className, '\\') + 1);
    }
}
