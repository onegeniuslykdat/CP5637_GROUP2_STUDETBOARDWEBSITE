<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\RowAction;

use Staatic\WordPress\ListTable\ValueAccessor;

final class RowAction implements RowActionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    private $href;

    private $visibleCallback;

    /**
     * @var mixed[]
     */
    private $arguments;

    public function __construct(string $name, string $label, $href, ?callable $visibleCallback = null, array $arguments = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->href = $href;
        $this->visibleCallback = $visibleCallback;
        $this->arguments = $arguments;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function href()
    {
        return $this->href;
    }

    public function visibleCallback()
    {
        return $this->visibleCallback;
    }

    public function arguments(): array
    {
        return $this->arguments;
    }

    public function render($item): string
    {
        $itemId = ValueAccessor::getValueByKey($item, 'id');
        $href = $this->href;
        $href = is_string($href) ? sprintf($href, $itemId) : $href($itemId);

        return sprintf(
            '<a href="%s"%s>%s</a>',
            esc_url($href),
            isset($this->arguments['class']) ? sprintf(' class="%s"', $this->arguments['class']) : '',
            $this->label
        );
    }
}
