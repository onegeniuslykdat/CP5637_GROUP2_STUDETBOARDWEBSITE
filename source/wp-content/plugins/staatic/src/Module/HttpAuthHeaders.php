<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

use Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting;
use Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting;

final class HttpAuthHeaders implements ModuleInterface
{
    /**
     * @var HttpAuthenticationUsernameSetting
     */
    private $httpAuthUsername;

    /**
     * @var HttpAuthenticationPasswordSetting
     */
    private $httpAuthPassword;

    public function __construct(HttpAuthenticationUsernameSetting $httpAuthUsername, HttpAuthenticationPasswordSetting $httpAuthPassword)
    {
        $this->httpAuthUsername = $httpAuthUsername;
        $this->httpAuthPassword = $httpAuthPassword;
    }

    public function hooks(): void
    {
        add_filter('cron_request', [$this, 'updateCronRequest'], 10);
        add_filter('http_request_args', [$this, 'updateHttpRequestArgs'], 10, 2);
    }

    /**
     * @param mixed[] $cronRequest
     */
    public function updateCronRequest($cronRequest): array
    {
        $httpAuthUsername = $this->httpAuthUsername->value();
        $httpAuthPassword = $this->httpAuthPassword->value();
        if (!$httpAuthUsername && !$httpAuthPassword) {
            return $cronRequest;
        }
        $cronRequest['args']['headers']['Authorization'] = sprintf(
            'Basic %s',
            base64_encode("{$httpAuthUsername}:{$httpAuthPassword}")
        );

        return $cronRequest;
    }

    /**
     * @param mixed[] $args
     */
    public function updateHttpRequestArgs($args, $url): array
    {
        $httpAuthUsername = $this->httpAuthUsername->value();
        $httpAuthPassword = $this->httpAuthPassword->value();
        if (!$httpAuthUsername && !$httpAuthPassword) {
            return $args;
        }
        if (!is_string($url) || !$this->isRequestToSelf($url)) {
            return $args;
        }
        $args['headers']['Authorization'] = sprintf(
            'Basic %s',
            base64_encode("{$httpAuthUsername}:{$httpAuthPassword}")
        );

        return $args;
    }

    private function isRequestToSelf(string $url): bool
    {
        $requestHost = parse_url($url, \PHP_URL_HOST);
        if (!$requestHost) {
            return \false;
        }
        if ($requestHost === 'localhost') {
            return \true;
        }
        $siteHost = parse_url(get_option('siteurl'), \PHP_URL_HOST);
        if ($siteHost && $siteHost === $requestHost) {
            return \true;
        }

        return \false;
    }
}
