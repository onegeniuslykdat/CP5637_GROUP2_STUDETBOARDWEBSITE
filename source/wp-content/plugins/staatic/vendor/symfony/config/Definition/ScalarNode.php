<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidTypeException;
class ScalarNode extends VariableNode
{
    /**
     * @param mixed $value
     */
    protected function validateType($value)
    {
        if (!\is_scalar($value) && null !== $value) {
            $ex = new InvalidTypeException(sprintf('Invalid type for path "%s". Expected "scalar", but got "%s".', $this->getPath(), get_debug_type($value)));
            if ($hint = $this->getInfo()) {
                $ex->addHint($hint);
            }
            $ex->setPath($this->getPath());
            throw $ex;
        }
    }
    /**
     * @param mixed $value
     */
    protected function isValueEmpty($value): bool
    {
        if ($this->isHandlingPlaceholder()) {
            return \false;
        }
        return null === $value || '' === $value;
    }
    protected function getValidPlaceholderTypes(): array
    {
        return ['bool', 'int', 'float', 'string'];
    }
}
