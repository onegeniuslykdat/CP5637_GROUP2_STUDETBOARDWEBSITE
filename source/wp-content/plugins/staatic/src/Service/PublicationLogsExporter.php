<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use RuntimeException;
use Staatic\WordPress\Logging\LogEntryRepository;
use Staatic\WordPress\Publication\PublicationRepository;

final class PublicationLogsExporter
{
    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var LogEntryRepository
     */
    private $logEntryRepository;

    public function __construct(PublicationRepository $publicationRepository, LogEntryRepository $logEntryRepository)
    {
        $this->publicationRepository = $publicationRepository;
        $this->logEntryRepository = $logEntryRepository;
    }

    public function __invoke(string $publicationId): void
    {
        if (!$publication = $this->publicationRepository->find($publicationId)) {
            throw new RuntimeException('Unable to find publication.');
        }
        $logEntries = $this->logEntryRepository->findByPublicationId($publication->id());
        $json = json_encode($logEntries, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
        header(
            sprintf('Content-Disposition: attachment; filename="staatic-logs-%s.json"', substr($publication->id(), -6))
        );
        header('Content-Type: application/json');
        header(sprintf('Content-Length: %d', strlen($json)));
        echo $json;
        die;
    }
}
