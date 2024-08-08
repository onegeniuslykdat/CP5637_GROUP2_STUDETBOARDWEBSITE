<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\UriResolver;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\CrawlUrlProvider\AdditionalUrlCrawlUrlProvider\AdditionalUrl;
use WP_Error;

class AdditionalUrls
{
    /**
     * @param string|null $value
     * @param UriInterface $baseUrl
     */
    public static function validateAndResolve($value, $baseUrl): array
    {
        $newValue = $resolvedValue = [];
        $errors = new WP_Error();
        foreach (explode("\n", (string) $value) as $line) {
            if (!($line = trim($line)) || strncmp($line, '#', strlen('#')) === 0) {
                $newValue[] = $line;

                continue;
            }
            [$url, $flags] = array_pad(str_getcsv($line, ' '), 2, null);
            if (!$url) {
                continue;
            }
            $authority = (new Uri($url))->getAuthority();
            if ($authority && $authority !== $baseUrl->getAuthority()) {
                $errors->add('invalid_additional_url', sprintf(
                    /* translators: 1: URL. */
                    __('URL "%1$s" is not part of this site.', 'staatic'),
                    esc_html($url)
                ));
                $newValue[] = "# {$line}";

                continue;
            }
            $newValue[] = $line;
            $resolvedUrl = UriResolver::resolve($baseUrl, new Uri($url));
            $dontTouch = $flags && strpos($flags, 'T') !== false;
            $dontFollow = $flags && strpos($flags, 'F') !== false;
            $dontSave = $flags && strpos($flags, 'S') !== false;
            $resolvedValue[(string) $resolvedUrl] = new AdditionalUrl(
                $resolvedUrl,
                'normal',
                $dontTouch,
                $dontFollow,
                $dontSave
            );
        }

        return [
            'newValue' => implode("\n", $newValue),
            'resolvedValue' => array_values($resolvedValue),
            'errors' => $errors
        ];
    }

    /**
     * @param string|null $value
     * @param UriInterface $baseUrl
     */
    public static function validate($value, $baseUrl): WP_Error
    {
        return self::validateAndResolve($value, $baseUrl)['errors'];
    }

    /** @return AdditionalUrl[]
     * @param string|null $value
     * @param UriInterface $baseUrl */
    public static function resolve($value, $baseUrl): array
    {
        return self::validateAndResolve($value, $baseUrl)['resolvedValue'];
    }
}
