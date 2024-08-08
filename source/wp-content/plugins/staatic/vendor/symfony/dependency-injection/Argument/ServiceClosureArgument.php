<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
class ServiceClosureArgument implements ArgumentInterface
{
    /**
     * @var mixed[]
     */
    private $values;
    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->values = [$value];
    }
    public function getValues(): array
    {
        return $this->values;
    }
    /**
     * @param mixed[] $values
     */
    public function setValues($values)
    {
        if ([0] !== array_keys($values)) {
            throw new InvalidArgumentException('A ServiceClosureArgument must hold one and only one value.');
        }
        $this->values = $values;
    }
}
