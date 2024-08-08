<?php

namespace Staatic\Crawler\CrawlUrlProvider\AdditionalPathCrawlUrlProvider;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\CrawlerInterface;
use Staatic\Crawler\CrawlUrl;
class AdditionalPath
{
    /**
     * @var string
     */
    private $priority = self::PRIORITY_NORMAL;
    /**
     * @var bool
     */
    private $dontTouch = \false;
    /**
     * @var bool
     */
    private $dontFollow = \false;
    /**
     * @var bool
     */
    private $dontSave = \false;
    /**
     * @var bool
     */
    private $recursive = \true;
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_LOW = 'low';
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $uriBasePath;
    public function __construct(string $path, string $uriBasePath, string $priority = self::PRIORITY_NORMAL, bool $dontTouch = \false, bool $dontFollow = \false, bool $dontSave = \false, bool $recursive = \true)
    {
        $this->priority = $priority;
        $this->dontTouch = $dontTouch;
        $this->dontFollow = $dontFollow;
        $this->dontSave = $dontSave;
        $this->recursive = $recursive;
        $this->path = self::normalizePath($path);
        $this->uriBasePath = ($uriBasePath === '/') ? '/' : rtrim($uriBasePath, '/\\');
    }
    /**
     * @param UriInterface $url
     */
    public function createCrawlUrl($url): CrawlUrl
    {
        return CrawlUrl::create($url, null, false, $this->tags());
    }
    public function path(): string
    {
        return $this->path;
    }
    public function uriBasePath(): string
    {
        return $this->uriBasePath;
    }
    public function priority(): string
    {
        return $this->priority;
    }
    public function dontTouch(): bool
    {
        return $this->dontTouch;
    }
    public function dontFollow(): bool
    {
        return $this->dontFollow;
    }
    public function dontSave(): bool
    {
        return $this->dontSave;
    }
    public function recursive(): bool
    {
        return $this->recursive;
    }
    private function tags(): array
    {
        $tags = [];
        if ($this->priority === self::PRIORITY_HIGH) {
            $tags[] = CrawlerInterface::TAG_PRIORITY_HIGH;
        } elseif ($this->priority === self::PRIORITY_LOW) {
            $tags[] = CrawlerInterface::TAG_PRIORITY_LOW;
        }
        if ($this->dontTouch) {
            $tags[] = CrawlerInterface::TAG_DONT_TOUCH;
        }
        if ($this->dontFollow) {
            $tags[] = CrawlerInterface::TAG_DONT_FOLLOW;
        }
        if ($this->dontSave) {
            $tags[] = CrawlerInterface::TAG_DONT_SAVE;
        }
        return $tags;
    }
    private static function normalizePath(string $path): string
    {
        if (\DIRECTORY_SEPARATOR === '\\') {
            $path = str_replace('\\', '/', $path);
        }
        if (substr($path, 1, 1) === ':') {
            $path = ucfirst($path);
        }
        return rtrim($path, '/\\');
    }
    /**
     * @param string $path
     * @param string $rootPath
     * @param string $rootUrlPath
     */
    public static function resolveUriBasePath($path, $rootPath, $rootUrlPath): ?string
    {
        $directory = is_dir($path) ? $path : dirname($path);
        $basePath = str_replace(self::normalizePath($rootPath), '', self::normalizePath($directory));
        if (!$basePath) {
            return null;
        }
        return (($rootUrlPath === '/') ? '' : $rootUrlPath) . $basePath;
    }
}
