<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Column;

final class TypeColumn extends AbstractColumn
{
    /**
     * @var mixed[]
     */
    private $types;

    public function __construct(string $name, string $label, array $types, array $arguments = [])
    {
        parent::__construct($name, $label, $arguments);
        $this->types = $types;
    }

    public function render($item): void
    {
        $itemValue = $this->itemValue($item);
        $result = $this->types[$itemValue] ?? null;
        if ($result) {
            $result = sprintf(
                '<span class="staatic-type-%s-%s">%s</span>',
                esc_attr($this->name),
                esc_attr($itemValue),
                esc_html($result)
            );
            echo $this->applyDecorators($result, $item);
        } else {
            echo '-';
        }
    }
}
