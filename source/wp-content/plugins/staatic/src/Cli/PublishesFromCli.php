<?php

declare(strict_types=1);

namespace Staatic\WordPress\Cli;

use RuntimeException;
use Staatic\WordPress\Logging\Contextable;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Publication\Task\CrawlTask;
use Staatic\WordPress\Publication\Task\DeployTask;
use Staatic\WordPress\Util\DateUtil;
use Staatic\WordPress\Util\TimeLimit;
use Throwable;
use WP_CLI;
use function WP_CLI\Utils\make_progress_bar;

trait PublishesFromCli
{
    /**
     * @param Publication $publication
     */
    protected function startPublication($publication): void
    {
        $this->logger->notice(__('Starting publication', 'staatic'), [
            'publicationId' => $publication->id()
        ]);
        if (!TimeLimit::setTimeLimit(0)) {
            $this->logger->warning('Unable to disable PHP time limit.');
        }
        $task = $this->taskProvider->firstTask();
        $publication->markInProgress();
        do {
            WP_CLI::line($task->description());
            if ($this->logger instanceof Contextable) {
                $this->logger->changeContext([
                    'publicationId' => $publication->id(),
                    'task' => $task::name()
                ]);
            }
            if ($this->exceedsPublicationTimeLimit($publication)) {
                $this->handleFailure(
                    $publication,
                    $task::name(),
                    new RuntimeException(__('The publication process took too long and was canceled.', 'staatic'))
                );
            }
            $publication->setCurrentTask($task::name());
            $this->publicationRepository->update($publication);
            $this->logger->info($task->description());

            try {
                do_action('staatic_publication_task_any', $publication, $task, \false);
                do_action_deprecated('staatic_publication_before_task', [[
                    'publication' => $publication,
                    'task' => $task
                ]], '1.4.4', 'staatic_publication_task_before');
                do_action('staatic_publication_task_before', $publication, $task);
            } catch (Throwable $failure) {
                $this->handleFailure($publication, $task::name(), $failure);
            }
            if (get_class($task) === CrawlTask::class) {
                $progress = make_progress_bar(__('Crawling...', 'staatic'), 0);
                $ticks = 0;
            } elseif (get_class($task) === DeployTask::class) {
                $progress = make_progress_bar(__('Deploying...', 'staatic'), 0);
                $ticks = 0;
            }
            do {
                try {
                    $taskFinished = $task->execute($publication, \true);
                    $this->updatePublicationUnlessCanceled($publication);
                } catch (Throwable $failure) {
                    $this->handleFailure($publication, $task::name(), $failure);
                }
                if (get_class($task) === CrawlTask::class) {
                    $addTicks = $publication->build()->numUrlsCrawled() - $ticks;
                    if ($addTicks) {
                        $progress->setTotal($publication->build()->numUrlsCrawlable());
                        $progress->tick($addTicks);
                        $ticks += $addTicks;
                    }
                } elseif (get_class($task) === DeployTask::class) {
                    $addTicks = $publication->deployment()->numResultsDeployed() - $ticks;
                    if ($addTicks) {
                        $progress->setTotal($publication->deployment()->numResultsDeployable());
                        $progress->tick($addTicks);
                        $ticks += $addTicks;
                    }
                }
                gc_collect_cycles();
            } while (!$taskFinished);

            try {
                do_action_deprecated('staatic_publication_after_task', [[
                    'publication' => $publication,
                    'task' => $task
                ]], '1.4.4', 'staatic_publication_task_after');
                do_action('staatic_publication_task_after', $publication, $task);
            } catch (Throwable $failure) {
                $this->handleFailure($publication, $task::name(), $failure);
            }
            if (get_class($task) === CrawlTask::class || get_class($task) === DeployTask::class) {
                $progress->finish();
            }
        } while ($publication->status()->isInProgress() && $task = $this->taskProvider->nextSupportedTask(
            $task,
            $publication
        ));
        $this->logger->notice(__('Finished publication', 'staatic'), [
            'publicationId' => $publication->id()
        ]);
        WP_CLI::success(sprintf(
            /* translators: %s: Date interval time taken. */
            __('Publication finished in %s!', 'staatic'),
            $this->formatter->difference($publication->dateCreated(), $publication->dateFinished())
        ));
    }

    /**
     * @param Publication $publication
     * @param string $taskName
     * @param Throwable $failure
     */
    protected function handleFailure($publication, $taskName, $failure): void
    {
        update_option('staatic_current_publication_id', '');
        $publication->markFailed();
        $this->publicationRepository->update($publication);
        $this->logger->critical(sprintf(
            /* translators: 1: Publication task. */
            __('Publication failed during %1$s task', 'staatic'),
            $taskName
        ), [
            'failure' => $failure
        ]);
        WP_CLI::error(sprintf(
            /* translators: 1: Publication task, 2: Error type, 3: Error message. */
            __('Publication failed during %1$s task with error %2$s: %3$s', 'staatic'),
            $taskName,
            get_class($failure),
            $failure->getMessage()
        ));
    }

    /**
     * @param Publication $publication
     */
    protected function updatePublicationUnlessCanceled($publication): void
    {
        if ($this->isPublicationCanceled($publication)) {
            $this->logger->warning(__('Publication has been canceled', 'staatic'));
            WP_CLI::error(__('Publication was canceled', 'staatic'));
        }
        $this->publicationRepository->update($publication);
    }

    /**
     * @param Publication $publication
     */
    protected function isPublicationCanceled($publication): bool
    {
        $freshPublication = $this->publicationRepository->find($publication->id());

        return $freshPublication->status()->isCanceled();
    }

    private function exceedsPublicationTimeLimit(Publication $publication): bool
    {
        return DateUtil::isDateNumHoursAgo(
            $publication->dateCreated(),
            apply_filters('staatic_publication_timeout', Publication::TIME_LIMIT_IN_HOURS)
        );
    }
}
