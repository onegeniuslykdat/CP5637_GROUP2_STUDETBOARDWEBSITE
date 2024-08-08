<?php

namespace Staatic\Crawler\CrawlUrlProvider;

use Generator;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\UriResolver;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\CrawlUrl;
use Staatic\Crawler\CrawlUrlProvider\AdditionalPathCrawlUrlProvider\AdditionalPath;
use Staatic\Crawler\DirectoryScanner\DirectoryScannerInterface;
use Staatic\Crawler\DirectoryScanner\StandardDirectoryScanner;
final class AdditionalPathCrawlUrlProvider implements CrawlUrlProviderInterface
{
    /**
     * @var iterable
     */
    private $additionalPaths;
    /**
     * @var UriInterface|null
     */
    private $baseUrl;
    /**
     * @var DirectoryScannerInterface
     */
    private $directoryScanner;
    /**
     * @param UriInterface|string|null $baseUrl
     */
    public function __construct(iterable $additionalPaths, $baseUrl = null, array $excludePaths = [], ?DirectoryScannerInterface $directoryScanner = null)
    {
        $this->additionalPaths = $additionalPaths;
        $this->baseUrl = is_string($baseUrl) ? new Uri($baseUrl) : $baseUrl;
        $this->directoryScanner = $directoryScanner ?: new StandardDirectoryScanner();
        $this->directoryScanner->setExcludePaths($excludePaths);
    }
    public function provide(): Generator
    {
        foreach ($this->additionalPaths as $additionalPath) {
            yield from $this->provideUsingPath($additionalPath);
        }
    }
    private function provideUsingPath(AdditionalPath $spec): Generator
    {
        $path = $spec->path();
        if (is_file($path)) {
            yield $this->convertPathToCrawlUrl($spec, $path);
        } elseif (is_dir($path)) {
            $iterator = $this->directoryScanner->scan($path, $spec->recursive());
            foreach ($iterator as $innerPath => $fileInfo) {
                yield $this->convertPathToCrawlUrl($spec, $innerPath);
            }
        }
    }
    private function convertPathToCrawlUrl(AdditionalPath $spec, string $path): CrawlUrl
    {
        $path = (\DIRECTORY_SEPARATOR === '/') ? $path : str_replace(\DIRECTORY_SEPARATOR, '/', $path);
        $relativePath = ($spec->path() === $path) ? basename($path) : str_replace("{$spec->path()}/", '', $path);
        $url = new Uri("{$spec->uriBasePath()}/{$relativePath}");
        $url = $this->baseUrl ? UriResolver::resolve($this->baseUrl, $url) : $url;
        return $spec->createCrawlUrl($url);
    }
}
