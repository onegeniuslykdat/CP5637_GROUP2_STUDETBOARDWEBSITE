<?php

namespace Staatic\Framework\BuildRepository;

use Staatic\Framework\Build;
interface BuildRepositoryInterface
{
    public function nextId(): string;
    /**
     * @param Build $build
     */
    public function add($build): void;
    /**
     * @param Build $build
     */
    public function update($build): void;
    /**
     * @param string $buildId
     */
    public function find($buildId): ?Build;
}
