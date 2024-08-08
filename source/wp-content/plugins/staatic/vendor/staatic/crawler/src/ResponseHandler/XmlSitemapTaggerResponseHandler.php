<?php

namespace Staatic\Crawler\ResponseHandler;

use Staatic\Crawler\CrawlerInterface;
use Staatic\Crawler\CrawlUrl;
class XmlSitemapTaggerResponseHandler extends AbstractResponseHandler
{
    private const SITEMAP_XML_TAG = CrawlerInterface::TAG_SITEMAP_XML;
    private const SITEMAP_XML_NAMES = ['sitemap.xml', 'sitemaps.xml', 'sitemap_index.xml', 'wp-sitemap.xml'];
    /**
     * @param CrawlUrl $crawlUrl
     */
    public function handle($crawlUrl): CrawlUrl
    {
        if ($this->isXmlSitemap($crawlUrl->url()->getPath())) {
            $crawlUrl = $crawlUrl->withTags(array_merge($crawlUrl->tags(), [self::SITEMAP_XML_TAG]));
        }
        return parent::handle($crawlUrl);
    }
    private function isXmlSitemap(string $path): bool
    {
        return in_array(basename($path), self::SITEMAP_XML_NAMES, \true) || substr_compare($path, '-sitemap.xml', -strlen('-sitemap.xml')) === 0;
    }
}
