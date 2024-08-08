<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use Staatic\Vendor\GuzzleHttp\Psr7\Exception\MalformedUriException;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\WordPress\Util\WordpressEnv;

final class SiteUrlProvider
{
    /**
     * @var bool
     */
    private $cached = \true;

    /**
     * @var UriInterface|null
     */
    private $siteUrl;

    public function __construct(bool $cached = \true)
    {
        $this->cached = $cached;
    }

    /**
     * Returns the site's home URL.
     */
    public function __invoke(): UriInterface
    {
        if (!$this->siteUrl || !$this->cached) {
            $this->siteUrl = $this->determineSiteUrl();
        }

        return $this->siteUrl;
    }

    private function determineSiteUrl(): UriInterface
    {
        $siteUrl = ($_ENV['STAATIC_SITE_URL'] ?? $_SERVER['STAATIC_SITE_URL'] ?? get_option(
            'staatic_override_site_url',
            null
        )) ?: WordpressEnv::getBaseUrl();
        $siteUrl = apply_filters('staatic_site_url', $siteUrl);

        return $this->convertSiteUrl($siteUrl);
    }

    private function convertSiteUrl(string $siteUrl): UriInterface
    {
        try {
            $result = new Uri($siteUrl);
        } catch (MalformedUriException $e) {
            $this->handleError($e->getMessage(), $siteUrl);
        }

        try {
            $urlPath = $result->getPath();
            $shouldEndWithSlash = WordpressEnv::getUrlsEndWithSlash();
            $endsWithSlash = substr_compare($urlPath, '/', -strlen('/')) === 0;
            if (!$urlPath) {
                $result = $result->withPath('/');
            } elseif ($shouldEndWithSlash && !$endsWithSlash) {
                $result = $result->withPath($urlPath . '/');
            } elseif (!$shouldEndWithSlash && $endsWithSlash && $urlPath !== '/') {
                $result = $result->withPath(mb_substr($urlPath, 0, -1));
            }
        } catch (MalformedUriException $e) {
            $this->handleError($e->getMessage(), $siteUrl);
        }

        return $result;
    }

    private function handleError(string $message, string $url): void
    {
        wp_die(sprintf(
            /* translators: 1: Error message, 2: Detected URL . */
            __('<strong>Staatic was unable to determine the site\'s base URL</strong><br><br>Got "<strong>%2$s</strong>", which resulted in the following error: <em>%1$s</em> - please check the following locations and ensure the configured URL is valid:<br><ul><li>the value of <code>WP_HOME</code> in the <code>wp-config.php</code> file;</li><li>the value of <code>home</code> in the <code>wp_options</code> database table;</li><li>the value of the <code>STAATIC_SITE_URL</code> environment variable;</li><li>any <code>staatic_site_url</code> or <code>home_url</code> filter hook implementation.</li></ul>', 'staatic'),
            $message,
            $url
        ));
    }
}
