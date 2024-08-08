<?php

namespace Staatic\Crawler;

use Staatic\Vendor\GuzzleHttp\ClientInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use SplSubject;
use Staatic\Crawler\CrawlOptions;
use Staatic\Crawler\CrawlProfile\CrawlProfileInterface;
use Staatic\Crawler\CrawlQueue\CrawlQueueInterface;
use Staatic\Crawler\CrawlUrlProvider\CrawlUrlProviderCollection;
use Staatic\Crawler\Event\EventInterface;
use Staatic\Crawler\KnownUrlsContainer\KnownUrlsContainerInterface;
use Staatic\Crawler\UrlTransformer\UrlTransformation;
interface CrawlerInterface extends SplSubject
{
    public const TAG_PRIORITY_HIGH = 'priority_high';
    public const TAG_PRIORITY_LOW = 'priority_low';
    public const TAG_DONT_TOUCH = 'dont_touch';
    public const TAG_DONT_FOLLOW = 'dont_follow';
    public const TAG_DONT_SAVE = 'dont_save';
    public const TAG_SITEMAP_XML = 'sitemap_xml';
    public const TAG_PAGE_NOT_FOUND = 'page_not_found';
    public const TAG_ENTRY_URL = 'entry_url';
    public const TAG_PROVIDED_URL = 'provided_url';
    public function __construct(ClientInterface $httpClient, CrawlProfileInterface $crawlProfile, CrawlQueueInterface $crawlQueue, KnownUrlsContainerInterface $knownUrlsContainer, CrawlOptions $crawlOptions);
    /**
     * @param CrawlUrlProviderCollection $crawlUrlProviders
     */
    public function initialize($crawlUrlProviders): int;
    public function crawl(): int;
    /**
     * @param UriInterface $resolvedUrl
     * @param mixed[] $context
     */
    public function shouldCrawl($resolvedUrl, $context = []): bool;
    /**
     * @param CrawlUrl $crawlUrl
     */
    public function addToCrawlQueue($crawlUrl): void;
    /**
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $context
     */
    public function transformUrl($url, $foundOnUrl = null, $context = []): UrlTransformation;
    public function crawlOptions(): CrawlOptions;
    public function numUrlsCrawlable(): int;
    public function getEvent(): ?EventInterface;
    /**
     * @param EventInterface $event
     */
    public function setEvent($event): void;
}
