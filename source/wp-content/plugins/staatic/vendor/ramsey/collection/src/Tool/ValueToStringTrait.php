<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Tool;

use DateTimeInterface;
use function assert;
use function get_resource_type;
use function is_array;
use function is_bool;
use function is_callable;
use function is_object;
use function is_resource;
use function is_scalar;
trait ValueToStringTrait
{
    /**
     * @param mixed $value
     */
    protected function toolValueToString($value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }
        if (is_array($value)) {
            return 'Array';
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        if (is_resource($value)) {
            return '(' . get_resource_type($value) . ' resource #' . (int) $value . ')';
        }
        assert(is_object($value));
        if (is_callable([$value, '__toString'])) {
            return (string) $value->__toString();
        }
        if ($value instanceof DateTimeInterface) {
            return $value->format('c');
        }
        return '(' . get_class($value) . ' Object)';
    }
}
