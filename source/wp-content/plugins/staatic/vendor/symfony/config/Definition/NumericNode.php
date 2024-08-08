<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
class NumericNode extends ScalarNode
{
    protected $min;
    protected $max;
    /**
     * @param int|float|null $min
     * @param int|float|null $max
     */
    public function __construct(?string $name, ?NodeInterface $parent = null, $min = null, $max = null, string $pathSeparator = BaseNode::DEFAULT_PATH_SEPARATOR)
    {
        parent::__construct($name, $parent, $pathSeparator);
        $this->min = $min;
        $this->max = $max;
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    protected function finalizeValue($value)
    {
        $value = parent::finalizeValue($value);
        $errorMsg = null;
        if (isset($this->min) && $value < $this->min) {
            $errorMsg = sprintf('The value %s is too small for path "%s". Should be greater than or equal to %s', $value, $this->getPath(), $this->min);
        }
        if (isset($this->max) && $value > $this->max) {
            $errorMsg = sprintf('The value %s is too big for path "%s". Should be less than or equal to %s', $value, $this->getPath(), $this->max);
        }
        if (isset($errorMsg)) {
            $ex = new InvalidConfigurationException($errorMsg);
            $ex->setPath($this->getPath());
            throw $ex;
        }
        return $value;
    }
    /**
     * @param mixed $value
     */
    protected function isValueEmpty($value): bool
    {
        return \false;
    }
}
