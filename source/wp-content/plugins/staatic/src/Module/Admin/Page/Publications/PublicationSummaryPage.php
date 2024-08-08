<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\Publications;

use Staatic\WordPress\Bridge\ResultRepository;
use Staatic\WordPress\Logging\LogEntry;
use Staatic\WordPress\Logging\LogEntryRepository;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Service\PartialRenderer;

final class PublicationSummaryPage implements ModuleInterface
{
    /**
     * @var AdminNavigation
     */
    private $navigation;

    /**
     * @var PartialRenderer
     */
    private $renderer;

    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var ResultRepository
     */
    private $resultRepository;

    /**
     * @var LogEntryRepository
     */
    private $logEntryRepository;

    /** @var string */
    public const PAGE_SLUG = 'staatic-publication';

    /**
     * @var Publication|null
     */
    private $publication;

    public function __construct(AdminNavigation $navigation, PartialRenderer $renderer, PublicationRepository $publicationRepository, ResultRepository $resultRepository, LogEntryRepository $logEntryRepository)
    {
        $this->navigation = $navigation;
        $this->renderer = $renderer;
        $this->publicationRepository = $publicationRepository;
        $this->resultRepository = $resultRepository;
        $this->logEntryRepository = $logEntryRepository;
    }

    public function hooks(): void
    {
        if (!is_admin()) {
            return;
        }
        $this->navigation->addPage(
            __('Publication Summary', 'staatic'),
            self::PAGE_SLUG,
            [$this, 'render'],
            'staatic_publish',
            PublicationsPage::PAGE_SLUG,
            [$this, 'load']
        );
    }

    public function load(): void
    {
        $publicationId = isset($_REQUEST['id']) ? sanitize_key($_REQUEST['id']) : null;
        if (!$publicationId) {
            wp_die(__('Missing publication id.', 'staatic'));
        }
        if (!$this->publication = $this->publicationRepository->find($publicationId)) {
            wp_die(__('Invalid publication.', 'staatic'));
        }
    }

    public function render(): void
    {
        [$logEntries, $breakLogEntryId] = $this->getLogSummary();
        $resultsPerStatusCategory = $this->resultRepository->getResultsPerStatusCategory(
            $this->publication->build()->id()
        );
        $this->renderer->render('admin/publication/summary.php', [
            'publication' => $this->publication,
            'logEntries' => $logEntries,
            'breakLogEntryId' => $breakLogEntryId,
            'resultsPerStatusCategory' => $resultsPerStatusCategory
        ]);
    }

    private function getLogSummary(): array
    {
        $numLogsTotal = 20;
        $numInitialLogs = 5;
        $initialLogEntries = $this->getFilteredLogEntries($this->publication->id(), $numInitialLogs + 1, 'ASC');
        $extraInitialEntryId = (count($initialLogEntries) > $numInitialLogs) ? array_pop(
            $initialLogEntries
        )->id() : null;
        $finalLogEntries = $this->getFilteredLogEntries(
            $this->publication->id(),
            $numLogsTotal - $numInitialLogs,
            'DESC'
        );
        $finalLogEntries = array_reverse($finalLogEntries);
        $initialLogEntryIds = array_map(function (LogEntry $entry) {
            return $entry->id();
        }, $initialLogEntries);
        $uniqueFinalLogEntries = array_filter($finalLogEntries, function (LogEntry $entry) use ($initialLogEntryIds) {
            return !in_array($entry->id(), $initialLogEntryIds);
        });
        $allLogEntries = array_merge($initialLogEntries, $uniqueFinalLogEntries);
        $found = \false;
        foreach ($finalLogEntries as $logEntry) {
            if ($logEntry->id() === $extraInitialEntryId) {
                $found = \true;

                break;
            }
        }
        $breakLogEntryId = (count($allLogEntries) >= $numLogsTotal && !$found) ? reset($finalLogEntries)->id() : null;

        return [$allLogEntries, $breakLogEntryId];
    }

    private function getFilteredLogEntries(string $publicationId, int $numEntries, string $direction = 'ASC'): array
    {
        return $this->logEntryRepository->findWhereMatching(
            $publicationId,
            ['notice', 'warning', 'error', 'critical'],
            null,
            $numEntries,
            0,
            'log_date',
            $direction
        );
    }
}
