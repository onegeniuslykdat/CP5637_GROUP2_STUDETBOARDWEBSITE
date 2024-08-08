<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use Staatic\Vendor\GuzzleHttp\Psr7\Exception\MalformedUriException;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use WP_Error;

class ExcludeUrls
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
            [$url] = array_pad(str_getcsv($line, ' '), 1, null);

            try {
                $authority = (new Uri($url))->getAuthority();
                if ($authority && $authority !== $baseUrl->getAuthority()) {
                    $errors->add('invalid_exclude_url', sprintf(
                        /* translators: 1: URL. */
                        __('URL "%1$s" is not part of this site.', 'staatic'),
                        esc_html($url)
                    ));
                    $newValue[] = "# {$line}";

                    continue;
                }
            } catch (MalformedUriException $exception) {
            }
            $newValue[] = $line;
            $resolvedValue[(string) $url] = $url;
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

    /** @return string[]
     * @param string|null $value
     * @param UriInterface $baseUrl */
    public static function resolve($value, $baseUrl): array
    {
        return self::validateAndResolve($value, $baseUrl)['resolvedValue'];
    }
}
