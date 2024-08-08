<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidTypeException;
class IntegerNode extends NumericNode
{
    /**
     * @param mixed $value
     */
    protected function validateType($value)
    {
        if (!\is_int($value)) {
            $ex = new InvalidTypeException(sprintf('Invalid type for path "%s". Expected "int", but got "%s".', $this->getPath(), get_debug_type($value)));
            if ($hint = $this->getInfo()) {
                $ex->addHint($hint);
            }
            $ex->setPath($this->getPath());
            throw $ex;
        }
    }
    protected function getValidPlaceholderTypes(): array
    {
        return ['int'];
    }
}
