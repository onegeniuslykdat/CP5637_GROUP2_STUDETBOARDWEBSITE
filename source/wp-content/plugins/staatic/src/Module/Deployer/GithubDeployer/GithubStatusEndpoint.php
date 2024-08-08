<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\GithubDeployer;

use DateTime;
use Exception;
use Staatic\Vendor\GuzzleHttp\Exception\ClientException;
use Staatic\WordPress\Factory\HttpClientFactory;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Service\Polyfill;
use WP_Error;
use WP_REST_Request;

final class GithubStatusEndpoint implements ModuleInterface
{
    /**
     * @var HttpClientFactory
     */
    private $httpClientFactory;

    public const NAMESPACE = 'staatic-github/v1';

    public const ENDPOINT = '/github-status';

    public function __construct(HttpClientFactory $httpClientFactory)
    {
        $this->httpClientFactory = $httpClientFactory;
    }

    public function hooks(): void
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes(): void
    {
        register_rest_route(self::NAMESPACE, self::ENDPOINT, [[
            'methods' => 'POST',
            'callback' => [$this, 'render'],
            'permission_callback' => [$this, 'permissionCallback'],
            'args' => []
        ]]);
    }

    /**
     * @param WP_REST_Request $request
     */
    public function render($request)
    {
        $params = json_decode($request->get_body(), \true);
        if (!isset($params['token'])) {
            return new WP_Error('staatic', __('Invalid request', 'staatic'), [
                'status' => 400
            ]);
        }
        $token = preg_replace('~[^a-zA-Z0-9_\-]~', '', $params['token']);

        return rest_ensure_response([
            'status' => $this->testConnection($token)
        ]);
    }

    private function testConnection(string $token): array
    {
        $httpClient = $this->httpClientFactory->createClient();
        $result = [
            'success' => \false,
            'message' => null,
            'detail' => null
        ];

        try {
            $response = $httpClient->request('GET', 'https://api.github.com/rate_limit', [
                'headers' => [
                    'Accept' => 'application/vnd.github+json',
                    'Authorization' => "token {$token}",
                    'X-GitHub-Api-Version' => '2022-11-28'
                ]
            ]);
            $content = json_decode($response->getBody()->getContents(), \true);
            $status = $content['resources']['core'] ?? [];
            $result['success'] = \true;
            $result['message'] = sprintf(
                /* translators: 1: Requests remaining, 2: Rate limit reset time, 3: Request limit. */
                __('Connection established; %1$s requests remaining until %2$s (limit: %3$s requests).', 'staatic'),
                $status['remaining'] ?? __('unknown', 'staatic'),
                isset($status['reset']) ? $this->formatTimestamp($status['reset']) : __('unknown', 'staatic'),
                $status['limit'] ?? __('unknown', 'staatic')
            );
        } catch (ClientException $e) {
            $result['message'] = __('Unable to authenticate with the provided credentials.', 'staatic');
            $result['detail'] = $e->getMessage();
        } catch (Exception $e) {
            $result['message'] = __('Unable to connect; please try again later.', 'staatic');
            $result['detail'] = $e->getMessage();
        }

        return $result;
    }

    private function formatTimestamp(int $timestamp): string
    {
        return (new DateTime())->setTimeStamp($timestamp)->setTimezone(Polyfill::wp_timezone())->format('H:i:s');
    }

    /**
     * @param WP_REST_Request $request
     */
    public function permissionCallback($request)
    {
        return current_user_can('staatic_manage_settings');
    }
}
