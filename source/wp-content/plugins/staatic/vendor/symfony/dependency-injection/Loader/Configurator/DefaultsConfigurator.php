<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutoconfigureTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\BindTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PublicTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
class DefaultsConfigurator extends AbstractServiceConfigurator
{
    use AutoconfigureTrait;
    use AutowireTrait;
    use BindTrait;
    use PublicTrait;
    public const FACTORY = 'defaults';
    /**
     * @var string|null
     */
    private $path;
    public function __construct(ServicesConfigurator $parent, Definition $definition, string $path = null)
    {
        parent::__construct($parent, $definition, null, []);
        $this->path = $path;
    }
    /**
     * @param string $name
     * @param mixed[] $attributes
     * @return static
     */
    final public function tag($name, $attributes = [])
    {
        if ('' === $name) {
            throw new InvalidArgumentException('The tag name in "_defaults" must be a non-empty string.');
        }
        $this->validateAttributes($name, $attributes);
        $this->definition->addTag($name, $attributes);
        return $this;
    }
    /**
     * @param string $fqcn
     */
    final public function instanceof($fqcn): InstanceofConfigurator
    {
        return $this->parent->instanceof($fqcn);
    }
    private function validateAttributes(string $tag, array $attributes, array $path = []): void
    {
        foreach ($attributes as $name => $value) {
            if (\is_array($value)) {
                $this->validateAttributes($tag, $value, array_merge($path, [$name]));
            } elseif (!\is_scalar($value ?? '')) {
                $name = implode('.', array_merge($path, [$name]));
                throw new InvalidArgumentException(sprintf('Tag "%s", attribute "%s" in "_defaults" must be of a scalar-type or an array of scalar-type.', $tag, $name));
            }
        }
    }
}
