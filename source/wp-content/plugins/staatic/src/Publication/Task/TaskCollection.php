<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication\Task;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use RuntimeException;
use Traversable;

final class TaskCollection implements IteratorAggregate
{
    /** @var array<string,TaskInterface> $tasks */
    private $tasks = [];

    public function __construct(array $tasks = [])
    {
        $this->add($tasks);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->tasks);
    }

    /** @param TaskInterface|mixed[] $tasks */
    public function add($tasks): self
    {
        $tasks = is_array($tasks) ? $tasks : [$tasks];
        foreach ($tasks as $task) {
            $this->tasks[$task::name()] = $task;
        }

        return $this;
    }

    /**
     * @param TaskInterface $task
     * @param string $beforeTask
     */
    public function addBefore($task, $beforeTask): self
    {
        $offset = array_search($beforeTask, array_keys($this->tasks));
        if ($offset === \false) {
            $this->tasks = [
                $task::name() => $task
            ] + $this->tasks;
        } else {
            $this->tasks = array_merge(array_slice($this->tasks, 0, $offset), [
                $task::name() => $task
            ], array_slice($this->tasks, $offset));
        }

        return $this;
    }

    /**
     * @param TaskInterface $task
     * @param string $afterTask
     */
    public function addAfter($task, $afterTask): self
    {
        $offset = array_search($afterTask, array_keys($this->tasks));
        if ($offset === \false) {
            $offset = count($this->tasks);
        }
        $this->tasks = array_merge(array_slice($this->tasks, 0, $offset + 1), [
            $task::name() => $task
        ], array_slice($this->tasks, $offset + 1));

        return $this;
    }

    /**
     * @param string $name
     */
    public function has($name): bool
    {
        return isset($this->tasks[$name]);
    }

    /**
     * @param string $name
     */
    public function get($name): TaskInterface
    {
        if (!isset($this->tasks[$name])) {
            throw new InvalidArgumentException("Task '{$name}' does not exist.");
        }

        return $this->tasks[$name];
    }

    public function first(): TaskInterface
    {
        foreach ($this->tasks as $task) {
            return $task;
        }

        throw new RuntimeException('No publication tasks are configured');
    }

    /**
     * @param string $name
     */
    public function after($name): TaskInterface
    {
        $keys = array_keys($this->tasks);
        $index = array_search($name, $keys);

        return isset($keys[$index + 1]) ? $this->tasks[$keys[$index + 1]] : null;
    }

    /**
     * @param string|mixed[] $names
     */
    public function forget($names): self
    {
        $names = is_array($names) ? $names : [$names];
        foreach ($names as $name) {
            unset($this->tasks[$name]);
        }

        return $this;
    }
}
