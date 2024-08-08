<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AbstractTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ArgumentTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutoconfigureTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\BindTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\CallTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ClassTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\DecorateTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\DeprecateTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FactoryTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FileTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\LazyTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ParentTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PublicTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ShareTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\SyntheticTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
class ServiceConfigurator extends AbstractServiceConfigurator
{
    use AbstractTrait;
    use ArgumentTrait;
    use AutoconfigureTrait;
    use AutowireTrait;
    use BindTrait;
    use CallTrait;
    use ClassTrait;
    use ConfiguratorTrait;
    use DecorateTrait;
    use DeprecateTrait;
    use FactoryTrait;
    use FileTrait;
    use LazyTrait;
    use ParentTrait;
    use PropertyTrait;
    use PublicTrait;
    use ShareTrait;
    use SyntheticTrait;
    use TagTrait;
    public const FACTORY = 'services';
    /**
     * @var ContainerBuilder
     */
    private $container;
    /**
     * @var mixed[]
     */
    private $instanceof;
    /**
     * @var bool
     */
    private $allowParent;
    /**
     * @var string|null
     */
    private $path;
    /**
     * @var bool
     */
    private $destructed = \false;
    public function __construct(ContainerBuilder $container, array $instanceof, bool $allowParent, ServicesConfigurator $parent, Definition $definition, ?string $id, array $defaultTags, string $path = null)
    {
        $this->container = $container;
        $this->instanceof = $instanceof;
        $this->allowParent = $allowParent;
        $this->path = $path;
        parent::__construct($parent, $definition, $id, $defaultTags);
    }
    public function __destruct()
    {
        if ($this->destructed) {
            return;
        }
        $this->destructed = \true;
        parent::__destruct();
        $this->container->removeBindings($this->id);
        $this->container->setDefinition($this->id, $this->definition->setInstanceofConditionals($this->instanceof));
    }
}
