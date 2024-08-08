<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\NetlifyDeployer;

use Exception;
use Staatic\Vendor\GuzzleHttp\Exception\ClientException;
use Staatic\WordPress\Factory\HttpClientFactory;
use Staatic\WordPress\Module\ModuleInterface;
use WP_Error;
use WP_REST_Request;

final class NetlifyStatusEndpoint implements ModuleInterface
{
    /**
     * @var HttpClientFactory
     */
    private $httpClientFactory;

    public const NAMESPACE = 'staatic-netlify/v1';

    public const ENDPOINT = '/netlify-status';

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
        $siteId = null;
        if (isset($params['siteId'])) {
            $siteId = preg_replace('~[^a-zA-Z0-9_\-]~', '', $params['siteId']);
        }

        return rest_ensure_response([
            'status' => $siteId ? $this->testConnectionWithSiteId($token, $siteId) : $this->testConnection($token)
        ]);
    }

    private function testConnectionWithSiteId(string $token, string $siteId): array
    {
        $httpClient = $this->httpClientFactory->createClient();
        $result = [
            'success' => \false,
            'message' => null,
            'detail' => null
        ];

        try {
            $response = $httpClient->request('GET', "https://api.netlify.com/api/v1/sites/{$siteId}", [
                'headers' => [
                    'Authorization' => "Bearer {$token}"
                ]
            ]);
            $content = json_decode($response->getBody()->getContents(), \true);
            $result['success'] = \true;
            $result['message'] = sprintf(
                /* translators: 1: Site name, 2: Site URL. */
                __('Connection established; identified site "%1$s" (%2$s).', 'staatic'),
                $content['name'],
                $content['url']
            );
        } catch (ClientException $e) {
            $result['message'] = __('Unable to authenticate with the provided credentials.', 'staatic_premium');
            $result['detail'] = $e->getMessage();
        } catch (Exception $e) {
            $result['message'] = __('Unable to connect; please try again later.', 'staatic_premium');
            $result['detail'] = $e->getMessage();
        }

        return $result;
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
            $response = $httpClient->request('GET', 'https://api.netlify.com/api/v1', [
                'headers' => [
                    'Authorization' => "Bearer {$token}"
                ]
            ]);
            $result['success'] = \true;
            $result['message'] = __('Connection established; please provide site ID.', 'staatic');
        } catch (ClientException $e) {
            $result['message'] = __('Unable to authenticate with the provided credentials.', 'staatic');
            $result['detail'] = $e->getMessage();
        } catch (Exception $e) {
            $result['message'] = __('Unable to connect; please try again later.', 'staatic');
            $result['detail'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * @param WP_REST_Request $request
     */
    public function permissionCallback($request)
    {
        return current_user_can('staatic_manage_settings');
    }
}
