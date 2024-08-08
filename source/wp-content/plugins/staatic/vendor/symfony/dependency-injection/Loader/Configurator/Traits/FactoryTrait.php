<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\Expression;
trait FactoryTrait
{
    /**
     * @param string|mixed[]|ReferenceConfigurator|Expression $factory
     * @return static
     */
    final public function factory($factory)
    {
        if (\is_string($factory) && 1 === substr_count($factory, ':')) {
            $factoryParts = explode(':', $factory);
            throw new InvalidArgumentException(sprintf('Invalid factory "%s": the "service:method" notation is not available when using PHP-based DI configuration. Use "[service(\'%s\'), \'%s\']" instead.', $factory, $factoryParts[0], $factoryParts[1]));
        }
        if ($factory instanceof Expression) {
            $factory = '@=' . $factory;
        }
        $this->definition->setFactory(static::processValue($factory, \true));
        return $this;
    }
}
