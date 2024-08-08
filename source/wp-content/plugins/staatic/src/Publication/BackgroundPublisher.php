<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication;

use Staatic\Vendor\Psr\Log\LoggerInterface;
use RuntimeException;
use Staatic\WordPress\Logging\Contextable;
use Staatic\WordPress\Publication\Task\RestartableTaskInterface;
use Staatic\WordPress\Publication\Task\TaskInterface;
use Staatic\WordPress\Util\DateUtil;
use Staatic\WordPress\Util\TimeLimit;
use Throwable;
use Staatic\Vendor\WP_Background_Process;

final class BackgroundPublisher extends WP_Background_Process
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var PublicationTaskProvider
     */
    private $publicationTaskProvider;

    /** @var int */
    public const DEFAULT_PROCESS_TIME_LIMIT = 300;

    /** @var int */
    protected $timeout;

    /** @var bool */
    protected $setTimeLimitSuccess = \false;

    /** @var int */
    protected $queue_lock_time = 60;

    /** @var string */
    protected $prefix = 'staatic';

    /** @var string */
    protected $action = 'background_publisher';

    /** @var int */
    protected $cron_interval = 1;

    // Execute as quickly as possible...
    /**
     * @var Publication|null
     */
    protected $publication;

    /**
     * @var TaskInterface|null
     */
    protected $task;

    public function __construct(LoggerInterface $logger, PublicationRepository $publicationRepository, PublicationTaskProvider $publicationTaskProvider)
    {
        $this->logger = $logger;
        $this->publicationRepository = $publicationRepository;
        $this->publicationTaskProvider = $publicationTaskProvider;
        if (is_multisite()) {
            $this->prefix = sprintf('%d_%s', get_current_blog_id(), $this->prefix);
        }
        parent::__construct();
    }

    /**
     * @param Publication $publication
     */
    public function initiatePublication($publication): bool
    {
        if (!$publication->status()->isPending()) {
            $this->logger->notice(sprintf(
                /* translators: %s: Publication ID. */
                __('Ignoring publication #%s; publication already started', 'staatic'),
                $publication->id()
            ));

            return \false;
        }
        $this->logger->notice(__('Starting publication', 'staatic'), [
            'publicationId' => $publication->id()
        ]);
        $firstTask = $this->publicationTaskProvider->firstTask();
        $publication->markInProgress();
        $this->publicationRepository->update($publication);
        if ($this->is_process_running()) {
            // Unlock background process if an old lock exists due to
            // a uncaught error or timeout.
            $this->unlock_process();
        }
        $this->push_to_queue($firstTask::name())->save()->dispatch();

        return \true;
    }

    /**
     * @param Publication $publication
     */
    public function cancelPublication($publication)
    {
        $currentPublicationId = get_option('staatic_current_publication_id');
        if (!$currentPublicationId || $currentPublicationId !== $publication->id()) {
            $this->logger->warning(__('Cannot cancel publication; publication has already finished', 'staatic'));

            return \false;
        }
        $this->logger->notice(__('Canceling publication', 'staatic'), [
            'publicationId' => $currentPublicationId
        ]);
        $this->cancel_process();
        $publication->markCanceled();
        $this->publicationRepository->update($publication);
        update_option('staatic_current_publication_id', '');

        return \true;
    }

    /**
     * Handle
     *
     * Pass each queue item to the task handler, while remaining
     * within server memory and time limit constraints.
     */
    protected function handle()
    {
        $this->timeout = apply_filters(
            'staatic_publication_task_timeout',
            (int) get_option('staatic_background_process_timeout')
        );
        ignore_user_abort(\true);
        add_filter("{$this->identifier}_time_exceeded", [$this, 'timeExceeded']);
        if (TimeLimit::setTimeLimit($this->timeout)) {
            $this->queue_lock_time = $this->timeout;
            $this->setTimeLimitSuccess = \true;
            add_filter("{$this->identifier}_default_time_limit", [$this, 'processTimeLimit']);
        }
        parent::handle();
    }

    public function timeExceeded($return)
    {
        if ($return) {
            $this->logger->debug("Background processing: time exceeded; resuming task in new process.");
        }

        return $return;
    }

    public function processTimeLimit($timeLimit)
    {
        if ($this->setTimeLimitSuccess) {
            return ($this->timeout === 0) ? self::DEFAULT_PROCESS_TIME_LIMIT : (int) ($this->timeout / 3);
        }

        return $timeLimit;
    }

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task($taskName)
    {
        $publicationId = get_option('staatic_current_publication_id');
        if (!$publicationId) {
            $this->logger->critical(sprintf(
                /* translators: %s: Publication task. */
                __('Current publication is unknown during task %s', 'staatic'),
                $taskName
            ));

            return \false;
        }
        $this->task = $this->publicationTaskProvider->getTask($taskName);
        if ($this->logger instanceof Contextable) {
            $this->logger->changeContext([
                'publicationId' => $publicationId,
                'task' => $taskName
            ]);
        }
        $this->publication = $this->publicationRepository->find($publicationId);
        if ($this->publication === null) {
            $this->logger->critical(sprintf(
                /* translators: %s: Publication ID, %2$s: Publication task. */
                __('Unable to find publication #%1$s for task %2$s', 'staatic'),
                $publicationId,
                $taskName
            ));

            return \false;
        }
        if (!$this->publication->currentTask()) {
            $this->logger->info(sprintf(
                /* translators: %s: Publication ID, %2$s: Publication task. */
                __('Using publication task timeout value of %d seconds', 'staatic'),
                $this->timeout
            ));
        }
        if ($this->exceedsPublicationTimeLimit($this->publication)) {
            $this->handleFailure(
                new RuntimeException(__('The publication process took too long and was canceled.', 'staatic'))
            );

            return \false;
        }
        $onSubsequentTask = $taskName !== $this->publication->currentTask();
        if ($onSubsequentTask) {
            $this->publication->setCurrentTask($taskName);
            $this->publicationRepository->update($this->publication);
            $this->logger->info($this->task->description());
        } elseif (!$this->task instanceof RestartableTaskInterface) {
            $this->handleFailure(
                new RuntimeException(
                    __('Task failed due to a timeout or fatal error; consider increasing "Publication Task Timeout" under Staatic > Settings > Advanced.', 'staatic')
                )
            );

            return \false;
        }

        try {
            do_action('staatic_publication_task_any', $this->publication, $this->task, \true);
            if ($onSubsequentTask) {
                do_action_deprecated('staatic_publication_before_task', [[
                    'publication' => $this->publication,
                    'task' => $this->task
                ]], '1.4.4', 'staatic_publication_task_before');
                do_action('staatic_publication_task_before', $this->publication, $this->task);
            }
        } catch (Throwable $failure) {
            $this->handleFailure($failure);

            return \false;
        }

        try {
            // We are always on limited resources when using the background publisher.
            $taskFinished = $this->task->execute($this->publication, \true);
            $this->updatePublicationUnlessCanceled($this->publication);
        } catch (Throwable $failure) {
            $this->handleFailure($failure);

            return \false;
        }
        // If the task has not finished, restart task.
        if ($taskFinished === \false) {
            return $taskName;
        }

        try {
            do_action_deprecated('staatic_publication_after_task', [[
                'publication' => $this->publication,
                'task' => $this->task
            ]], '1.4.4', 'staatic_publication_task_after');
            do_action('staatic_publication_task_after', $this->publication, $this->task);
        } catch (Throwable $failure) {
            $this->handleFailure($failure);

            return \false;
        }
        // Otherwise find the next task.
        $nextTask = $this->publication->status()->isInProgress() ? $this->publicationTaskProvider->nextSupportedTask(
            $this->task,
            $this->publication
        ) : null;
        // Continue with next task or quit.
        if ($nextTask) {
            return $nextTask::name();
        }
        $this->logger->notice(__('Finished publication', 'staatic'), [
            'publicationId' => $publicationId
        ]);

        return \false;
    }

    /**
     * @param Throwable $failure
     */
    protected function handleFailure($failure): void
    {
        update_option('staatic_current_publication_id', '');
        $this->publication->markFailed();
        $this->publicationRepository->update($this->publication);
        $this->logger->critical(sprintf(
            /* translators: 1: Publication task. */
            __('Publication failed during %1$s task', 'staatic'),
            $this->task::name()
        ), [
            'failure' => $failure
        ]);
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete()
    {
        parent::complete();
        update_option('staatic_current_publication_id', '');
    }

    /**
     * @param Publication $publication
     */
    protected function updatePublicationUnlessCanceled($publication): void
    {
        if ($this->isPublicationCanceled($publication)) {
            return;
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
