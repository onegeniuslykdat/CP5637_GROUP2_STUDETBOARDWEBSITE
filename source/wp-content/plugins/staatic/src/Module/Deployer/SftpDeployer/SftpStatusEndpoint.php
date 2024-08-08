<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\SftpDeployer;

use Exception;
use Staatic\Vendor\phpseclib3\Crypt\PublicKeyLoader;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedAlgorithmException;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedCurveException;
use Staatic\Vendor\phpseclib3\Net\SFTP;
use Staatic\WordPress\Module\ModuleInterface;
use WP_Error;
use WP_REST_Request;

final class SftpStatusEndpoint implements ModuleInterface
{
    public const NAMESPACE = 'staatic-sftp/v1';

    public const ENDPOINT = '/sftp-status';

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
        if (!isset($params['host']) || !isset($params['port']) || !isset($params['password']) && !isset($params['sshKey'])) {
            return new WP_Error('staatic', __('Invalid request', 'staatic'), [
                'status' => 400
            ]);
        }

        return rest_ensure_response([
            'status' => $this->testConnection(
                $params['host'],
                (int) $params['port'],
                $params['username'],
                $params['password'] ?: null,
                $params['sshKey'] ?: null,
                $params['sshKeyPassword'] ?: null
            )
        ]);
    }

    private function testConnection(
        string $host,
        int $port,
        string $username,
        ?string $password,
        ?string $sshKey,
        ?string $sshKeyPassword
    ): array
    {
        //!TODO: refactor, add test method to deploy strategy interface.
        $result = [
            'success' => \false,
            'message' => null,
            'detail' => null
        ];

        try {
            $sftp = new SFTP($host, $port);
            if (!$sftp->login(
                $username,
                $sshKey ? PublicKeyLoader::load($sshKey, $sshKeyPassword ?: \false) : $password
            )) {
                throw new SftpLoginException('Login was unsuccessful.');
            }
            $result['success'] = \true;
            $result['message'] = sprintf(
                /* translators: 1: Current working directory. */
                __('Connection established (entry directory: "%s").', 'staatic'),
                esc_html($sftp->pwd())
            );
        } catch (SftpLoginException|UnsupportedCurveException|UnsupportedAlgorithmException $e) {
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
