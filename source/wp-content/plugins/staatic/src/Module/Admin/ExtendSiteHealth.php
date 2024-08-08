<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin;

use DateTimeImmutable;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Request\TestRequest;
use Staatic\WordPress\Service\Formatter;
use Staatic\WordPress\Service\HealthChecks;
use Staatic\WordPress\Service\SiteUrlProvider;
use Staatic\WordPress\Util\WordpressEnv;

final class ExtendSiteHealth implements ModuleInterface
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var HealthChecks
     */
    private $healthChecks;

    /**
     * @var SiteUrlProvider
     */
    private $siteUrlProvider;

    public function __construct(Formatter $formatter, HealthChecks $healthChecks, SiteUrlProvider $siteUrlProvider)
    {
        $this->formatter = $formatter;
        $this->healthChecks = $healthChecks;
        $this->siteUrlProvider = $siteUrlProvider;
    }

    public function hooks(): void
    {
        if (!is_admin() || is_network_admin()) {
            return;
        }
        add_filter('site_status_tests', [$this, 'addSiteStatusTests']);
        add_filter('debug_information', [$this, 'addDebugInformation']);
    }

    /**
     * @param mixed[] $tests
     */
    public function addSiteStatusTests($tests): array
    {
        $tests['direct']['staatic_permalink_structure'] = [
            'label' => __('Permalink structure', 'staatic'),
            'test' => [$this->healthChecks, 'permalinkStructureTest']
        ];
        $tests['direct']['staatic_writable_work_directory'] = [
            'label' => __('Writable work directory', 'staatic'),
            'test' => [$this->healthChecks, 'writableWorkDirectoryTest']
        ];
        $tests['direct']['staatic_publication_task_timeout'] = [
            'label' => __('Publication task timeout', 'staatic'),
            'test' => [$this->healthChecks, 'publicationTaskTimeoutTest']
        ];
        $tests['async']['staatic_loopback_requests'] = [
            'label' => __('Staatic loopback requests', 'staatic'),
            'test' => rest_url('staatic-health/v1/tests/loopback-requests'),
            'has_rest' => \true,
            'async_direct_test' => [$this->healthChecks, 'loopbackRequestsTest']
        ];

        return $tests;
    }

    /**
     * @param mixed[] $info
     */
    public function addDebugInformation($info): array
    {
        $htmlDomParsers = [
            'html5' => 'HTML5-PHP',
            'dom_wrap' => 'PHP DOM Wrapper',
            'simple_html' => 'Simple Html Dom Parser'
        ];
        $sslVerifyBehaviors = [
            'enabled' => __('Enabled', 'staatic'),
            'disabled' => __('Disabled', 'staatic'),
            'path' => __('Enabled using custom certificate', 'staatic')
        ];
        $htmlDomParser = get_option('staatic_crawler_dom_parser');
        $processNotFound = get_option('staatic_crawler_process_not_found');
        $lowercaseUrls = get_option('staatic_crawler_lowercase_urls');
        $deploymentMethod = get_option('staatic_deployment_method');
        $downgradeHttps = get_option('staatic_http_https_to_http');
        $sslVerifyBehavior = get_option('staatic_ssl_verify_behavior');
        $sslVerifyPath = get_option('staatic_ssl_verify_path');
        $testRequestStatus = get_option(TestRequest::OPTION_NAME);
        $info['staatic'] = [
            'label' => __('Staatic', 'staatic'),
            'fields' => [
                'version' => [
                    'label' => __('Version', 'staatic'),
                    'value' => \STAATIC_VERSION
                ],
                'site_url' => [
                    'label' => __('Site URL', 'staatic'),
                    'value' => (string) ($this->siteUrlProvider)()
                ],
                'wordpress_url' => [
                    'label' => __('WordPress URL', 'staatic'),
                    'value' => WordpressEnv::getWordpressUrl()
                ],
                'destination_url' => [
                    'label' => __('Destination URL', 'staatic'),
                    'value' => get_option('staatic_destination_url') ?: __('Undefined', 'staatic')
                ],
                'deployment_method' => [
                    'label' => __('Deployment method', 'staatic'),
                    'value' => $deploymentMethod ? ucfirst($deploymentMethod) : __('Undefined', 'staatic')
                ],
                'process_not_found' => [
                    'label' => __('Process "Page not found" resources', 'staatic'),
                    'value' => $processNotFound ? __('Enabled', 'staatic') : __('Disabled', 'staatic')
                ],
                'lowercase_urls' => [
                    'label' => __('Lowercase URLs', 'staatic'),
                    'value' => $lowercaseUrls ? __('Enabled', 'staatic') : __('Disabled', 'staatic')
                ],
                'http_auth_username' => [
                    'label' => __('HTTP auth username', 'staatic'),
                    'value' => get_option('staatic_http_auth_username') ?: __('Undefined', 'staatic')
                ],
                'http_concurrency' => [
                    'label' => __('HTTP concurrency', 'staatic'),
                    'value' => get_option('staatic_http_concurrency') ?: __('Undefined', 'staatic')
                ],
                'http_https_to_http' => [
                    'label' => __('Downgrade HTTPS to HTTP while crawling site', 'staatic'),
                    'value' => ($downgradeHttps === null) ? __('Undefined', 'staatic') : ($downgradeHttps ? __('Enabled', 'staatic') : __('Disabled', 'staatic'))
                ],
                'http_timeout' => [
                    'label' => __('HTTP timeout', 'staatic'),
                    'value' => get_option('staatic_http_timeout') ?: __('Undefined', 'staatic')
                ],
                'ssl_verify_behavior' => [
                    'label' => __('SSL verification', 'staatic'),
                    'value' => $sslVerifyBehaviors[$sslVerifyBehavior] ?? $sslVerifyBehavior ?? __('Undefined', 'staatic'),
                    'debug' => $sslVerifyBehavior
                ],
                'ssl_verify_path' => [
                    'label' => __('CA bundle path', 'staatic'),
                    'value' => $sslVerifyPath ?: __('Undefined', 'staatic')
                ],
                'html_dom_parser' => [
                    'label' => __('HTML DOM parser', 'staatic'),
                    'value' => $htmlDomParsers[$htmlDomParser] ?? $htmlDomParser ?? __('Undefined', 'staatic')
                ],
                'publication_task_timeout' => [
                    'label' => __('Publication task timeout', 'staatic'),
                    'value' => get_option('staatic_background_process_timeout') ?: __('Undefined', 'staatic')
                ],
                'test_request_status' => [
                    'label' => __('Publication test task', 'staatic'),
                    'value' => $this->formatTestRequestStatus($testRequestStatus),
                    'debug' => $testRequestStatus
                ]
            ]
        ];

        return $info;
    }

    private function formatTestRequestStatus($status): string
    {
        if (!$status) {
            return __('Unknown', 'staatic');
        }
        [$start, $last, $done] = array_pad(explode('_', $status, 3), 3, null);
        $start = $start ? (int) $start : null;
        $last = $last ? (int) $last : null;
        if (!$last || time() - $start <= TestRequest::TIME_LIMIT) {
            return sprintf(
                /* translators: 1: Test request start time. */
                __('Pending (started %1$s)', 'staatic'),
                $this->formatter->shortDate(new DateTimeImmutable("@{$start}"))
            );
        }
        $diff = $last - $start;

        return sprintf(
            /* translators: 1: Run time in seconds, 2: Test request start time. */
            __('Duration of %1$s seconds (last executed %2$s)', 'staatic'),
            $this->formatter->number($diff),
            $this->formatter->shortDate(new DateTimeImmutable("@{$start}"))
        );
    }
}
