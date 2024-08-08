<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication;

use InvalidArgumentException;
use Staatic\WordPress\Publication\Task\TaskCollection;
use Staatic\WordPress\Publication\Task\TaskInterface;

final class PublicationTaskProvider
{
    /**
     * @var TaskCollection
     */
    private $tasks;

    /**
     * @var bool
     */
    private $initialized = \false;

    /** @param TaskInterface[] $publicationTasks */
    public function __construct(iterable $publicationTasks)
    {
        if (empty($publicationTasks)) {
            throw new InvalidArgumentException('No publication tasks provided.');
        }
        $this->tasks = new TaskCollection(iterator_to_array($publicationTasks));
    }

    public function getTasks(): TaskCollection
    {
        if (!$this->initialized) {
            /**
             * Filters the list of publication tasks that are to be executed during
             * the execution of a publication.
             *
             * @since 1.10.0
             *
             * @param TaskCollection $tasks List of tasks as a task collection.
             */
            $this->tasks = apply_filters('staatic_publication_tasks', $this->tasks);
            $this->initialized = \true;
        }

        return $this->tasks;
    }

    public function getTask(string $name): TaskInterface
    {
        return $this->getTasks()->get($name);
    }

    public function firstTask(): TaskInterface
    {
        return $this->getTasks()->first();
    }

    public function nextTask(TaskInterface $currentTask): ?TaskInterface
    {
        return $this->getTasks()->after($currentTask::name());
    }

    public function nextSupportedTask(TaskInterface $currentTask, Publication $publication): ?TaskInterface
    {
        $task = $currentTask;
        do {
            $task = $this->nextTask($task);
        } while ($task && !$task->supports($publication));

        return $task;
    }
}
