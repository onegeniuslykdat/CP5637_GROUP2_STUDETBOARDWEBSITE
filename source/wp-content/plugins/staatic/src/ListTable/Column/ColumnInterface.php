<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Column;

interface ColumnInterface
{
    public function name(): string;

    public function label(): string;

    public function isSortable(): bool;

    public function sortDefinition(): array;

    public function isHiddenByDefault(): bool;

    public function render($item): void;
}
