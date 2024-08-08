<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Column;

use Staatic\WordPress\Service\Formatter;

final class LogMessageColumn extends AbstractColumn
{
    /**
     * @var Formatter
     */
    private $formatter;

    public function __construct(Formatter $formatter, string $name, string $label, array $arguments = [])
    {
        $this->formatter = $formatter;
        parent::__construct($name, $label, $arguments);
    }

    public function render($item): void
    {
        $value = $this->itemValue($item);
        $result = $this->formatter->logMessage($value);
        echo $this->applyDecorators($result, $item);
    }
}
