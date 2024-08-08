<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use InvalidArgumentException;
use UnitEnum;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
class EnumNode extends ScalarNode
{
    /**
     * @var mixed[]
     */
    private $values;
    public function __construct(?string $name, ?NodeInterface $parent = null, array $values = [], string $pathSeparator = BaseNode::DEFAULT_PATH_SEPARATOR)
    {
        if (!$values) {
            throw new InvalidArgumentException('$values must contain at least one element.');
        }
        foreach ($values as $value) {
            if (null === $value || \is_scalar($value)) {
                continue;
            }
            if (!$value instanceof UnitEnum) {
                throw new InvalidArgumentException(sprintf('"%s" only supports scalar, enum, or null values, "%s" given.', __CLASS__, get_debug_type($value)));
            }
            if (get_class($value) !== ($enumClass = $enumClass ?? get_class($value))) {
                throw new InvalidArgumentException(sprintf('"%s" only supports one type of enum, "%s" and "%s" passed.', __CLASS__, $enumClass, get_class($value)));
            }
        }
        parent::__construct($name, $parent, $pathSeparator);
        $this->values = $values;
    }
    public function getValues()
    {
        return $this->values;
    }
    /**
     * @param string $separator
     */
    public function getPermissibleValues($separator): string
    {
        return implode($separator, array_unique(array_map(static function ($value): string {
            if (!$value instanceof UnitEnum) {
                return json_encode($value);
            }
            return ltrim(var_export($value, \true), '\\');
        }, $this->values)));
    }
    /**
     * @param mixed $value
     */
    protected function validateType($value)
    {
        if ($value instanceof UnitEnum) {
            return;
        }
        parent::validateType($value);
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    protected function finalizeValue($value)
    {
        $value = parent::finalizeValue($value);
        if (!\in_array($value, $this->values, \true)) {
            $ex = new InvalidConfigurationException(sprintf('The value %s is not allowed for path "%s". Permissible values: %s', json_encode($value), $this->getPath(), $this->getPermissibleValues(', ')));
            $ex->setPath($this->getPath());
            throw $ex;
        }
        return $value;
    }
}
