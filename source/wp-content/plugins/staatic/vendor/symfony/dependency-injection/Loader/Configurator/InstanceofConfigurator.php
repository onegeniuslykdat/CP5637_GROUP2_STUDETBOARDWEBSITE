<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\BindTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\CallTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\LazyTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PublicTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ShareTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
class InstanceofConfigurator extends AbstractServiceConfigurator
{
    use AutowireTrait;
    use BindTrait;
    use CallTrait;
    use ConfiguratorTrait;
    use LazyTrait;
    use PropertyTrait;
    use PublicTrait;
    use ShareTrait;
    use TagTrait;
    public const FACTORY = 'instanceof';
    /**
     * @var string|null
     */
    private $path;
    public function __construct(ServicesConfigurator $parent, Definition $definition, string $id, string $path = null)
    {
        parent::__construct($parent, $definition, $id, []);
        $this->path = $path;
    }
    /**
     * @param string $fqcn
     */
    final public function instanceof($fqcn): self
    {
        return $this->parent->instanceof($fqcn);
    }
}
