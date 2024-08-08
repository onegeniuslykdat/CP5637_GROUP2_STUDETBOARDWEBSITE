<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use BadMethodCallException;
use UnitEnum;
use Staatic\Vendor\Symfony\Component\Config\Loader\ParamConfigurator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Alias;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Parameter;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\Expression;
abstract class AbstractConfigurator
{
    public const FACTORY = 'unknown';
    public static $valuePreProcessor;
    /**
     * @var Definition|Alias|null
     */
    protected $definition = null;
    public function __call(string $method, array $args)
    {
        if (method_exists($this, 'set' . $method)) {
            return $this->{'set' . $method}(...$args);
        }
        throw new BadMethodCallException(sprintf('Call to undefined method "%s::%s()".', static::class, $method));
    }
    public function __sleep(): array
    {
        throw new BadMethodCallException('Cannot serialize ' . __CLASS__);
    }
    public function __wakeup()
    {
        throw new BadMethodCallException('Cannot unserialize ' . __CLASS__);
    }
    /**
     * @param mixed $value
     * @param bool $allowServices
     * @return mixed
     */
    public static function processValue($value, $allowServices = \false)
    {
        if (\is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = static::processValue($v, $allowServices);
            }
            return self::$valuePreProcessor ? (self::$valuePreProcessor)($value, $allowServices) : $value;
        }
        if (self::$valuePreProcessor) {
            $value = (self::$valuePreProcessor)($value, $allowServices);
        }
        if ($value instanceof ReferenceConfigurator) {
            $reference = new Reference($value->id, $value->invalidBehavior);
            return ($value instanceof ClosureReferenceConfigurator) ? new ServiceClosureArgument($reference) : $reference;
        }
        if ($value instanceof InlineServiceConfigurator) {
            $def = $value->definition;
            $value->definition = null;
            return $def;
        }
        if ($value instanceof ParamConfigurator) {
            return (string) $value;
        }
        if ($value instanceof self) {
            throw new InvalidArgumentException(sprintf('"%s()" can be used only at the root of service configuration files.', $value::FACTORY));
        }
        switch (\true) {
            case null === $value:
            case \is_scalar($value):
            case $value instanceof UnitEnum:
                return $value;
            case $value instanceof ArgumentInterface:
            case $value instanceof Definition:
            case $value instanceof Expression:
            case $value instanceof Parameter:
            case $value instanceof AbstractArgument:
            case $value instanceof Reference:
                if ($allowServices) {
                    return $value;
                }
        }
        throw new InvalidArgumentException(sprintf('Cannot use values of type "%s" in service configuration files.', get_debug_type($value)));
    }
}
