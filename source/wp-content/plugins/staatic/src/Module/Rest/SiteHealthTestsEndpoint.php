<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Rest;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Service\HealthChecks;
use WP_REST_Request;

final class SiteHealthTestsEndpoint implements ModuleInterface
{
    /**
     * @var HealthChecks
     */
    private $healthChecks;

    public const NAMESPACE = 'staatic-health/v1';

    public const ENDPOINT_LOOPBACK_REQUESTS = '/tests/loopback-requests';

    public function __construct(HealthChecks $healthChecks)
    {
        $this->healthChecks = $healthChecks;
    }

    public function hooks(): void
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes(): void
    {
        register_rest_route(self::NAMESPACE, self::ENDPOINT_LOOPBACK_REQUESTS, [[
            'methods' => 'GET',
            'callback' => [$this->healthChecks, 'loopbackRequestsTest'],
            'permission_callback' => [$this, 'permissionCallback'],
            'args' => []
        ]]);
    }

    /**
     * @param WP_REST_Request $request
     */
    public function permissionCallback($request)
    {
        return current_user_can('view_site_health_checks');
    }
}
