<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Column;

use Staatic\WordPress\ListTable\ValueAccessor;

abstract class AbstractColumn implements ColumnInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string|null
     */
    protected $sortColumn;

    /**
     * @var string|null
     */
    protected $sortDirection;

    /**
     * @var bool
     */
    protected $isHiddenByDefault;

    /** @var DecoratorInterface[] */
    protected $decorators = [];

    /**
     * @var string|null
     */
    protected $getter;

    public function __construct(string $name, string $label, array $arguments = [])
    {
        $this->name = $name;
        $this->label = $label;
        $arguments = array_merge([
            'sortColumn' => $this->defaultSortColumn(),
            'sortDirection' => $this->defaultSortDirection(),
            'isHiddenByDefault' => \false,
            'decorators' => [],
            'getter' => null
        ], $arguments);
        $this->sortColumn = $arguments['sortColumn'];
        $this->sortDirection = $arguments['sortDirection'];
        $this->isHiddenByDefault = $arguments['isHiddenByDefault'];
        $this->decorators = $arguments['decorators'];
        $this->getter = $arguments['getter'];
    }

    public function name(): string
    {
        return $this->name;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function isSortable(): bool
    {
        return $this->sortColumn !== null;
    }

    public function sortDefinition(): array
    {
        return [$this->sortColumn, $this->sortDirection];
    }

    public function isHiddenByDefault(): bool
    {
        return $this->isHiddenByDefault;
    }

    public function defaultSortColumn(): ?string
    {
        return $this->name;
    }

    public function defaultSortDirection(): string
    {
        return 'ASC';
    }

    public function itemValue($item)
    {
        return ValueAccessor::getValueByKeyRecursive($item, $this->name, $this->getter);
    }

    /**
     * @param string|null $input
     */
    protected function applyDecorators($input, $item): string
    {
        foreach ($this->decorators as $decorator) {
            $input = $decorator->decorate($input, $item);
        }

        return $input;
    }
}
