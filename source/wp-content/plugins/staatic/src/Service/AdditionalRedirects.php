<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use Staatic\Vendor\GuzzleHttp\Psr7\Exception\MalformedUriException;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Framework\PostProcessor\AdditionalRedirectsPostProcessor\AdditionalRedirect;
use WP_Error;

class AdditionalRedirects
{
    /**
     * @param string|null $value
     */
    public static function validateAndResolve($value): array
    {
        $newValue = $resolvedValue = [];
        $errors = new WP_Error();
        foreach (explode("\n", (string) $value) as $line) {
            if (!($line = trim($line)) || strncmp($line, '#', strlen('#')) === 0) {
                $newValue[] = $line;

                continue;
            }
            [$origin, $redirectUrl, $statusCode] = array_pad(str_getcsv($line, ' '), 3, null);
            if (!$origin) {
                continue;
            }

            try {
                $originUrl = new Uri($origin);
            } catch (MalformedUriException $exception) {
                $errors->add('invalid_additional_redirect', sprintf(
                    /* translators: 1: Redirect origin. */
                    __('Redirect "%1$s" has a malformed origin.', 'staatic'),
                    esc_html($origin)
                ));
                $newValue[] = "# {$line}";

                continue;
            }
            if ($originUrl->getScheme() || $originUrl->getAuthority()) {
                $errors->add('invalid_additional_redirect', sprintf(
                    /* translators: 1: Redirect origin. */
                    __('Redirect "%1$s" should not contain scheme or authority part.', 'staatic'),
                    esc_html($origin)
                ));
                $newValue[] = "# {$line}";

                continue;
            }
            if (strncmp($originUrl->getPath(), '/', strlen('/')) !== 0) {
                $errors->add('invalid_additional_redirect', sprintf(
                    /* translators: 1: Redirect origin. */
                    __('Redirect "%1$s" does not have an absolute origin path.', 'staatic'),
                    esc_html($origin)
                ));
                $newValue[] = "# {$line}";

                continue;
            }

            try {
                $redirectUrl = new Uri($redirectUrl);
            } catch (MalformedUriException $exception) {
                $errors->add('invalid_additional_redirect', sprintf(
                    /* translators: 1: Redirect origin, 2: Redirect URL. */
                    __('Redirect "%1$s" has a malformed redirect URL: %2$s.', 'staatic'),
                    esc_html($origin),
                    esc_html($redirectUrl)
                ));
                $newValue[] = "# {$line}";

                continue;
            }
            if ($statusCode && !in_array($statusCode, [301, 302, 307, 308])) {
                $errors->add('invalid_additional_redirect', sprintf(
                    /* translators: 1: Redirect origin, 2: HTTP status code. */
                    __('Redirect "%1$s" has an invalid HTTP status code: "%2$s".', 'staatic'),
                    $origin,
                    esc_html($statusCode)
                ));
                $newValue[] = "# {$line}";

                continue;
            }
            $newValue[] = $line;
            $resolvedValue[(string) $origin] = new AdditionalRedirect(
                $originUrl->getPath(),
                $redirectUrl,
                (int) ($statusCode ?: 302)
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
     */
    public static function validate($value): WP_Error
    {
        return self::validateAndResolve($value)['errors'];
    }

    /** @return AdditionalRedirect[]
     * @param string|null $value */
    public static function resolve($value): array
    {
        return self::validateAndResolve($value)['resolvedValue'];
    }
}
