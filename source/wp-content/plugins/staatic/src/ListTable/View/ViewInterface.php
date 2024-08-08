<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\View;

interface ViewInterface
{
    public function name(): string;

    public function label(): string;
}
