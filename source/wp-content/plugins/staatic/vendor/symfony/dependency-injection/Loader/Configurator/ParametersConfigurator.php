<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\Expression;
class ParametersConfigurator extends AbstractConfigurator
{
    public const FACTORY = 'parameters';
    /**
     * @var ContainerBuilder
     */
    private $container;
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }
    /**
     * @param string $name
     * @param mixed $value
     * @return static
     */
    final public function set($name, $value)
    {
        if ($value instanceof Expression) {
            throw new InvalidArgumentException(sprintf('Using an expression in parameter "%s" is not allowed.', $name));
        }
        $this->container->setParameter($name, static::processValue($value, \true));
        return $this;
    }
    /**
     * @param mixed $value
     * @return static
     */
    final public function __invoke(string $name, $value)
    {
        return $this->set($name, $value);
    }
}
