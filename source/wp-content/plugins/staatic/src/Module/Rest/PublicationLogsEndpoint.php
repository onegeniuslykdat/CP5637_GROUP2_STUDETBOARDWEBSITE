<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Rest;

use Staatic\WordPress\Logging\LogEntry;
use Staatic\WordPress\Logging\LogEntryRepository;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Service\Formatter;
use WP_Error;
use WP_REST_Request;

final class PublicationLogsEndpoint implements ModuleInterface
{
    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var LogEntryRepository
     */
    private $logEntryRepository;

    /**
     * @var Formatter
     */
    private $formatter;

    public const NAMESPACE = 'staatic/v1';

    public const ENDPOINT = '/publication-logs';

    public function __construct(PublicationRepository $publicationRepository, LogEntryRepository $logEntryRepository, Formatter $formatter)
    {
        $this->publicationRepository = $publicationRepository;
        $this->logEntryRepository = $logEntryRepository;
        $this->formatter = $formatter;
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
        $publicationId = $params['id'] ?? null;
        if (!$publicationId) {
            return new WP_Error('staatic', __('Invalid request', 'staatic'), [
                'status' => 400
            ]);
        }
        $publication = $this->publicationRepository->find($publicationId);
        if (!$publication) {
            wp_send_json_error();
        }
        $publicationLogs = $this->logEntryRepository->findLatestByPublicationId($publicationId);

        return rest_ensure_response([
            'publication' => [
                'id' => $publication->id(),
                'status' => $publication->status()->status()
            ],
            'logs' => array_map([$this, 'transformLogEntry'], $publicationLogs)
        ]);
    }

    private function transformLogEntry(LogEntry $logEntry): array
    {
        $source = $logEntry->context() ? $logEntry->context()['source'] ?? null : null;

        return [
            'id' => $logEntry->id(),
            'date' => $logEntry->date()->format('c'),
            'dateFormatted' => $this->formatter->shortDate($logEntry->date()),
            'level' => $logEntry->level(),
            'source' => $source,
            'message' => $logEntry->message()
        ];
    }

    /**
     * @param WP_REST_Request $request
     */
    public function permissionCallback($request)
    {
        return current_user_can('staatic_publish');
    }
}
