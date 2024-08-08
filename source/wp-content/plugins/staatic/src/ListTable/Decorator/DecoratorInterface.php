<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Decorator;

interface DecoratorInterface
{
    /**
     * @param string $input
     */
    public function decorate($input, $item): string;
}
