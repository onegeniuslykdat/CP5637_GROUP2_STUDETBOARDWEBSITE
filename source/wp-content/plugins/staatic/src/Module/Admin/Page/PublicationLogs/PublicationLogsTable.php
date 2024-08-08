<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\PublicationLogs;

use Staatic\Vendor\Psr\Log\LogLevel;
use Staatic\WordPress\Bootstrap;
use Staatic\WordPress\ListTable\AbstractListTable;
use Staatic\WordPress\ListTable\Column\DateColumn;
use Staatic\WordPress\ListTable\Column\LogMessageColumn;
use Staatic\WordPress\ListTable\Column\TextColumn;
use Staatic\WordPress\ListTable\Decorator\CallbackDecorator;
use Staatic\WordPress\ListTable\View\View;
use Staatic\WordPress\Logging\LogEntry;
use Staatic\WordPress\Logging\LogEntryRepository;
use Staatic\WordPress\Service\Formatter;

class PublicationLogsTable extends AbstractListTable
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var LogEntryRepository
     */
    private $repository;

    /**
     * @var bool
     */
    private $isDebug = \false;

    /**
     * @var bool
     */
    private $showSensitiveData = \false;

    /** @var string */
    protected const NAME = 'log_entry_list_table';

    public function __construct(Formatter $formatter, LogEntryRepository $repository)
    {
        $this->formatter = $formatter;
        $this->repository = $repository;
        parent::__construct('id', ['log_date', 'ASC']);
    }

    /**
     * @param string $baseUrl
     * @param mixed[] $arguments
     */
    public function initialize($baseUrl, $arguments = []): void
    {
        parent::initialize($baseUrl, $arguments);
        $this->isDebug = Bootstrap::instance()->isDebug();
        $this->showSensitiveData = current_user_can('staatic_manage_settings');
        $this->setupColumns();
        $this->setupViews();
    }

    private function setupColumns(): void
    {
        $this->addColumns([new LogMessageColumn($this->formatter, 'message', __('Message', 'staatic'), [
            'decorators' => [new CallbackDecorator(function (string $input, LogEntry $item) {
                        return $this->logMessageOutput($input, $item);
                    })]
        ]), new TextColumn('log_level', __('Level', 'staatic'), [
            'getter' => 'level'
        ]), new DateColumn($this->formatter, 'log_date', __('Date', 'staatic'), [
            'getter' => 'date'
        ])]);
    }

    private function logMessageOutput(string $input, LogEntry $entry): string
    {
        $result = sprintf('<span class="message">%s</span>', $input);
        $context = $entry->context();
        $details = [];
        if ($this->isDebug && $this->showSensitiveData) {
            if (isset($context['sourceFile'], $context['sourceLine'])) {
                $shortSourceFile = str_replace(\ABSPATH, './', $context['sourceFile']);
                $details[] = esc_html("Source: {$shortSourceFile}:{$context['sourceLine']}");
            }
            if (isset($context['sourceClass'], $context['sourceFunction'])) {
                $details[] = esc_html("Function: {$context['sourceClass']}::{$context['sourceFunction']}");
            }
            if (isset($context['memory'])) {
                $details[] = esc_html(sprintf('Memory: %s', size_format($context['memory'])));
            }
        }
        if (isset($context['timeTaken'])) {
            $details[] = esc_html(sprintf('Request time: %sms', number_format_i18n($context['timeTaken'] * 1000, 2)));
        }
        if (!empty($details)) {
            $result .= sprintf('<br><span class="staatic-log-debug">%s</span>', implode('<br>', $details));
        }
        if (isset($context['failure'])) {
            $failureSummary = (preg_match(
                '~\w+:\s+(.+?) in .+?:\d+\n~s',
                $context['failure'],
                $match
            ) === 1) ? $match[1] : null;
            if ($this->showSensitiveData) {
                if ($failureSummary) {
                    $result .= sprintf('<details class="staatic-publication-error">
                            <summary>%1$s</summary>
                            <p>%2$s</p>
                        </details>', esc_html($failureSummary), nl2br(esc_html($context['failure'])));
                } else {
                    $result .= sprintf('<div class="staatic-publication-error">%1$s</div>', esc_html($failureSummary));
                }
            } elseif ($failureSummary) {
                $result .= sprintf('<div class="staatic-publication-error">%1$s</div>', esc_html($failureSummary));
            }
        }

        return sprintf('<div class="staatic-log-level-%s">%s</div>', esc_attr($entry->level()), $result);
    }

    public function setupViews(): void
    {
        $logLevels = [
            LogLevel::DEBUG => __('Debug', 'staatic'),
            LogLevel::INFO => __('Informational', 'staatic'),
            LogLevel::NOTICE => __('Notice', 'staatic'),
            LogLevel::WARNING => __('Warning', 'staatic'),
            LogLevel::ERROR => __('Error', 'staatic'),
            LogLevel::CRITICAL => __('Critical', 'staatic')
        ];
        foreach ($logLevels as $name => $label) {
            $this->addView(new View($name, $label));
        }
    }

    public function views(): array
    {
        $views = parent::views();
        if (!$this->repository->countWhereMatching($this->arguments['publicationId'], LogLevel::DEBUG)) {
            unset($views[LogLevel::DEBUG]);
        }

        return $views;
    }

    /**
     * @param string|null $view
     * @param string|null $query
     * @param int $limit
     * @param int $offset
     * @param string|null $orderBy
     * @param string|null $direction
     */
    public function items($view, $query, $limit, $offset, $orderBy, $direction): array
    {
        return $this->repository->findWhereMatching(
            $this->arguments['publicationId'],
            $view,
            $query,
            $limit,
            $offset,
            $orderBy,
            $direction
        );
    }

    /**
     * @param string|null $view
     * @param string|null $query
     */
    public function numItems($view, $query): int
    {
        return $this->repository->countWhereMatching($this->arguments['publicationId'], $view, $query);
    }

    public function numItemsPerView(): ?array
    {
        return $this->repository->getLogEntriesPerLevel($this->arguments['publicationId']);
    }
}
