<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\RowAction;

interface RowActionInterface
{
    public function name(): string;

    public function label(): string;

    public function href();

    public function visibleCallback();

    public function render($item): string;
}
