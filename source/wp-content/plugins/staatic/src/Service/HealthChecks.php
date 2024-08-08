<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use Staatic\WordPress\Factory\HttpClientFactory;
use Staatic\WordPress\Module\Admin\Page\TestRequestPage;
use Staatic\WordPress\Module\Admin\Page\SettingsPage;
use Staatic\WordPress\Request\TestRequest;
use Throwable;

final class HealthChecks
{
    /**
     * @var HttpClientFactory
     */
    private $httpClientFactory;

    /**
     * @var SiteUrlProvider
     */
    private $siteUrlProvider;

    /** @var int */
    private const TEST_REQUEST_MINIMUM_RUNTIME = 180;

    public function __construct(HttpClientFactory $httpClientFactory, SiteUrlProvider $siteUrlProvider)
    {
        $this->httpClientFactory = $httpClientFactory;
        $this->siteUrlProvider = $siteUrlProvider;
    }

    public function permalinkStructureTest()
    {
        $status = get_option('permalink_structure');
        if (!$status) {
            return $this->buildTestReport([
                'label' => __('Permalink structure is not configured correctly', 'staatic'),
                'status' => 'critical',
                'description' => __('<p>In order to successfully generate a static version of your WordPress site, a permalink structure other than Plain needs to be configured.</p>', 'staatic'),
                'actions' => sprintf(
                    /* translators: 1: Link to Permalink Settings. */
                    __('<p>Please ensure a <a href="%1$s">Permalink Structure</a> other than Plain is selected.</p>', 'staatic'),
                    admin_url('options-permalink.php')
                ),
                'test' => 'staatic_permalink_structure'
            ]);
        }

        return $this->buildTestReport([
            'label' => __('Permalink structure appears to be configured correctly', 'staatic'),
            'status' => 'good',
            'description' => __('<p>In order to successfully generate a static version of your WordPress site, a permalink structure compatible with static sites needs to be configured.</p>', 'staatic'),
            'test' => 'staatic_permalink_structure'
        ]);
    }

    public function writableWorkDirectoryTest()
    {
        $workDirectory = get_option('staatic_work_directory');
        if (is_dir($workDirectory)) {
            if (!is_writable($workDirectory)) {
                return $this->buildTestReport([
                    'label' => __('Staatic work directory is not writable', 'staatic'),
                    'status' => 'critical',
                    'description' => __('<p>In order to successfully generate a static version of your WordPress site, the Staatic work directory needs to be writable.</p>', 'staatic'),
                    'actions' => sprintf(
                        /* translators: 1: Link to Advanced Settings. */
                        __('<p>Please ensure that the Work Directory configured under <a href="%1$s">Advanced Settings</a> is writable.</p>', 'staatic'),
                        admin_url(sprintf('admin.php?page=%s&group=staatic-advanced', SettingsPage::PAGE_SLUG))
                    ),
                    'test' => 'staatic_writable_work_directory'
                ]);
            }
        } elseif (!is_writable(dirname($workDirectory))) {
            return $this->buildTestReport([
                'label' => __('Staatic work directory is not writable and can\'t be created', 'staatic'),
                'status' => 'critical',
                'description' => __('<p>In order to successfully generate a static version of your WordPress site, the Staatic work directory needs to be writable.</p>', 'staatic'),
                'actions' => sprintf(
                    /* translators: 1: Link to Advanced Settings. */
                    __('<p>Please ensure that the Work Directory, configured under <a href="%1$s">Advanced Settings</a>, is writable.</p>', 'staatic'),
                    admin_url(sprintf('admin.php?page=%s&group=staatic-advanced', SettingsPage::PAGE_SLUG))
                ),
                'test' => 'staatic_writable_work_directory'
            ]);
        }

        return $this->buildTestReport([
            'label' => __('Staatic work directory is writable', 'staatic'),
            'status' => 'good',
            'description' => __('<p>The Staatic work directory is used by Staatic to write publication resources and other temporary files.</p>', 'staatic'),
            'test' => 'staatic_writable_work_directory'
        ]);
    }

    public function publicationTaskTimeoutTest()
    {
        $introduction = __('<p>The publication test task is used to verify the amount of time publication tasks are able to run without timing out. When the task times out too early, publications started from WP-Admin may not have enough time to finish and as a result fail.</p>', 'staatic');
        $status = (string) get_option(TestRequest::OPTION_NAME);
        if (!$status) {
            return $this->buildTestReport([
                'label' => __('Publication test task has not yet started', 'staatic'),
                'status' => 'recommended',
                'description' => $introduction . __('<p>Since the task has not yet started, the publication task run time could not be verified.</p>', 'staatic'),
                'actions' => sprintf(
                    /* translators: 1: Link to Trigger Test Request. */
                    __('<p>Please ensure WP-Cron is functioning correctly. Alternatively, <a href="%1$s">trigger the test manually</a>.</p>', 'staatic'),
                    admin_url(sprintf('admin.php?page=%s', TestRequestPage::PAGE_SLUG))
                ),
                'test' => 'staatic_publication_task_timeout'
            ]);
        }
        [$start, $last, $done] = array_pad(explode('_', $status, 3), 3, null);
        $start = $start ? (int) $start : null;
        $last = $last ? (int) $last : null;
        if (!$last || time() - $start <= TestRequest::TIME_LIMIT) {
            return $this->buildTestReport([
                'label' => __('Publication test task has not yet finished', 'staatic'),
                'status' => 'good',
                'description' => $introduction . __('<p>Since the task has not yet finished, the publication task run time could not be verified.</p>', 'staatic'),
                'test' => 'staatic_publication_task_timeout'
            ]);
        }
        $diff = $last - $start;
        if ($diff <= self::TEST_REQUEST_MINIMUM_RUNTIME) {
            return $this->buildTestReport([
                'label' => __('Publication test task reveals a low timeout value', 'staatic'),
                'status' => 'recommended',
                'description' => $introduction . sprintf(
                    /* translators: 1: Test run time in seconds. */
                    __('<p>The publication test task stopped after <strong>%1$s seconds</strong>. This can be caused by a variety of reasons, including a low PHP <code>maximum_execution_time</code> value or too strict web server request time limits.</p>', 'staatic'),
                    (string) $diff
                ),
                'actions' => sprintf(
                    /* translators: 1: Recommended minimum run time in seconds, 2: Link to Documentation. */
                    __('<p>Please ensure that HTTP requests and PHP scripts are allowed to run at least %1$s seconds, or consider publishing using the <code>staatic publish</code> <a href="%2$s" target="_blank" rel="noopener">WP-CLI command</a> instead.</p>', 'staatic'),
                    self::TEST_REQUEST_MINIMUM_RUNTIME,
                    'https://staatic.com/wordpress/documentation/'
                ),
                'test' => 'staatic_publication_task_timeout'
            ]);
        }

        return $this->buildTestReport([
            'label' => __('Publication task timeout appears to be in order', 'staatic'),
            'status' => 'good',
            'description' => __('<p>When starting a publication from within WP-Admin, Staatic executes the publication process in multiple separate tasks, which require enough time to complete successfully.</p>', 'staatic'),
            'test' => 'staatic_publication_task_timeout'
        ]);
    }

    public function loopbackRequestsTest()
    {
        $httpClient = $this->httpClientFactory->createInternalClient([], \false);
        $introduction = __('<p>Loopback requests are used by the Staatic crawler component to generate the static version of your WordPress site.</p>', 'staatic');

        try {
            $httpClient->request('GET', ($this->siteUrlProvider)(), [
                'headers' => [
                    'Accept' => 'text/html, application/xhtml+xml, application/xml;q=0.9, */*;q=0.8'
                ],
                'timeout' => 10
            ]);
        } catch (Throwable $e) {
            return $this->buildTestReport([
                'label' => __('Staatic is unable to perform loopback requests', 'staatic'),
                'status' => 'critical',
                'description' => $introduction . sprintf(
                    /* translators: 1: Error message. */
                    __('<p>A test request resulted in the following error:</p><p><code>%1$s</code></p>', 'staatic'),
                    esc_html($e->getMessage())
                ),
                'actions' => sprintf(
                    /* translators: 1: Link to Advanced Settings. */
                    __('<p>Please ensure that your server’s IP address is whitelisted and that HTTP authentication credentials are valid under <a href="%1$s">Advanced Settings</a>, in case HTTP authentication is enabled.</p>', 'staatic'),
                    admin_url(sprintf('admin.php?page=%s&group=staatic-advanced', SettingsPage::PAGE_SLUG))
                ),
                'test' => 'staatic_loopback_requests'
            ]);
        }

        return $this->buildTestReport([
            'label' => __('Staatic can perform loopback requests', 'staatic'),
            'status' => 'good',
            'description' => $introduction,
            'test' => 'staatic_loopback_requests'
        ]);
    }

    private function buildTestReport(array $args): array
    {
        $args = array_merge([
            'status' => 'recommended',
            'badge' => [
                'label' => __('Staatic', 'staatic'),
                'color' => ($args['status'] === 'good') ? 'blue' : 'red'
            ]
        ], $args);
        if (!empty($args['actions'])) {
            $args['actions'] .= '<p class="staatic-site-health-signature"><img src="' . esc_url(
                plugin_dir_url(\STAATIC_FILE) . 'assets/logo.svg'
            ) . '" alt="" height="20" width="20" class="staatic-site-health-signature-icon">' . sprintf(
                /* translators: 1: expands to 'Staatic' */
                esc_html__('This was reported by %1$s', 'staatic'),
                'Staatic'
            ) . '</p>';
        }

        return $args;
    }
}
