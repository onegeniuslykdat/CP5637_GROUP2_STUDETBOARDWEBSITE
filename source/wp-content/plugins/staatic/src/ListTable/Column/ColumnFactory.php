<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Column;

use InvalidArgumentException;
use Staatic\WordPress\Service\Formatter;

final class ColumnFactory
{
    /**
     * @var Formatter
     */
    private $formatter;

    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function create(string $class, array $options, array $arguments = []): ColumnInterface
    {
        switch ($class) {
            case DateColumn::class:
                return new DateColumn($this->formatter, $options['name'], $options['label'], $arguments);

                break;
            case TextColumn::class:
                return new TextColumn($options['name'], $options['label'], $arguments);

                break;
            case TypeColumn::class:
                return new TypeColumn(
                    $options['name'],
                    $options['label'],
                    $options['types'],
                    $options['colors'] ?? [],
                    $arguments
                );

                break;
            case UserColumn::class:
                return new UserColumn($options['name'], $options['label'], $arguments);

                break;
            default:
                throw new InvalidArgumentException("Column type '{$class}' is not supported");

                break;
        }
    }
}
