<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\BulkAction;

final class BulkAction implements BulkActionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /** @var callable */
    private $callback;

    public function __construct(string $name, string $label, ?callable $callback = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->callback = $callback;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function callback(): callable
    {
        return $this->callback;
    }
}
