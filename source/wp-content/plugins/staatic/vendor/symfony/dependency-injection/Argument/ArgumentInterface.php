<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

interface ArgumentInterface
{
    public function getValues(): array;
    /**
     * @param mixed[] $values
     */
    public function setValues($values);
}
