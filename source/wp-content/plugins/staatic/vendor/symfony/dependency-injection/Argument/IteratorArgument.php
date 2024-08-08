<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

class IteratorArgument implements ArgumentInterface
{
    /**
     * @var mixed[]
     */
    private $values;
    public function __construct(array $values)
    {
        $this->setValues($values);
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
        $this->values = $values;
    }
}
