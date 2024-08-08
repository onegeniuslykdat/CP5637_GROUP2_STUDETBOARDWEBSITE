<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use Closure;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
class VariableNode extends BaseNode implements PrototypeNodeInterface
{
    protected $defaultValueSet = \false;
    protected $defaultValue;
    protected $allowEmptyValue = \true;
    /**
     * @param mixed $value
     */
    public function setDefaultValue($value)
    {
        $this->defaultValueSet = \true;
        $this->defaultValue = $value;
    }
    public function hasDefaultValue(): bool
    {
        return $this->defaultValueSet;
    }
    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        $v = $this->defaultValue;
        return ($v instanceof Closure) ? $v() : $v;
    }
    /**
     * @param bool $boolean
     */
    public function setAllowEmptyValue($boolean)
    {
        $this->allowEmptyValue = $boolean;
    }
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    /**
     * @param mixed $value
     */
    protected function validateType($value)
    {
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    protected function finalizeValue($value)
    {
        if (!$this->allowEmptyValue && $this->isHandlingPlaceholder() && $this->finalValidationClosures) {
            $e = new InvalidConfigurationException(sprintf('The path "%s" cannot contain an environment variable when empty values are not allowed by definition and are validated.', $this->getPath()));
            if ($hint = $this->getInfo()) {
                $e->addHint($hint);
            }
            $e->setPath($this->getPath());
            throw $e;
        }
        if (!$this->allowEmptyValue && $this->isValueEmpty($value)) {
            $ex = new InvalidConfigurationException(sprintf('The path "%s" cannot contain an empty value, but got %s.', $this->getPath(), json_encode($value)));
            if ($hint = $this->getInfo()) {
                $ex->addHint($hint);
            }
            $ex->setPath($this->getPath());
            throw $ex;
        }
        return $value;
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    protected function normalizeValue($value)
    {
        return $value;
    }
    /**
     * @param mixed $leftSide
     * @param mixed $rightSide
     * @return mixed
     */
    protected function mergeValues($leftSide, $rightSide)
    {
        return $rightSide;
    }
    /**
     * @param mixed $value
     */
    protected function isValueEmpty($value): bool
    {
        return empty($value);
    }
}
