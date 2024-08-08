<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\BulkAction;

interface BulkActionInterface
{
    public function name(): string;

    public function label(): string;

    public function callback();
}
