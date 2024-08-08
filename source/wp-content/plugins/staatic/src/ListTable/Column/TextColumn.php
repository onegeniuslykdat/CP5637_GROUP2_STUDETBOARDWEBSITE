<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Column;

final class TextColumn extends AbstractColumn
{
    /**
     * @var string
     */
    protected $emptyValue;

    /**
     * @var int|null
     */
    protected $maxLength;

    /**
     * @var int|null
     */
    protected $maxLines;

    public function __construct(string $name, string $label, array $arguments = [])
    {
        parent::__construct($name, $label, $arguments);
        $this->emptyValue = $arguments['emptyValue'] ?? '-';
        $this->maxLength = $arguments['maxLength'] ?? null;
        $this->maxLines = $arguments['maxLines'] ?? null;
    }

    public function render($item): void
    {
        $result = $this->itemValue($item);
        if ($result) {
            $result = esc_html($result);
            if ($this->maxLength) {
                $result = sprintf(
                    '<div class="staatic-max-length" style="max-width: %dch;">%s</div>',
                    $this->maxLength,
                    $result
                );
            }
            if ($this->maxLines) {
                $result = sprintf(
                    '<div class="staatic-max-lines" style="-webkit-line-clamp: %d;">%s</div>',
                    $this->maxLines,
                    $result
                );
            }
            echo $this->applyDecorators($result, $item);
        } else {
            echo $this->emptyValue;
        }
    }
}
