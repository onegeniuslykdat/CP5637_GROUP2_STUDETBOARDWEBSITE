<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AbstractTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ArgumentTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutoconfigureTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\BindTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\CallTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\DeprecateTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FactoryTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\LazyTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ParentTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PublicTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ShareTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
class PrototypeConfigurator extends AbstractServiceConfigurator
{
    use AbstractTrait;
    use ArgumentTrait;
    use AutoconfigureTrait;
    use AutowireTrait;
    use BindTrait;
    use CallTrait;
    use ConfiguratorTrait;
    use DeprecateTrait;
    use FactoryTrait;
    use LazyTrait;
    use ParentTrait;
    use PropertyTrait;
    use PublicTrait;
    use ShareTrait;
    use TagTrait;
    public const FACTORY = 'load';
    /**
     * @var PhpFileLoader
     */
    private $loader;
    /**
     * @var string
     */
    private $resource;
    /**
     * @var mixed[]|null
     */
    private $excludes;
    /**
     * @var bool
     */
    private $allowParent;
    /**
     * @var string|null
     */
    private $path;
    public function __construct(ServicesConfigurator $parent, PhpFileLoader $loader, Definition $defaults, string $namespace, string $resource, bool $allowParent, string $path = null)
    {
        $definition = new Definition();
        if (!$defaults->isPublic() || !$defaults->isPrivate()) {
            $definition->setPublic($defaults->isPublic());
        }
        $definition->setAutowired($defaults->isAutowired());
        $definition->setAutoconfigured($defaults->isAutoconfigured());
        $definition->setBindings(unserialize(serialize($defaults->getBindings())));
        $definition->setChanges([]);
        $this->loader = $loader;
        $this->resource = $resource;
        $this->allowParent = $allowParent;
        $this->path = $path;
        parent::__construct($parent, $definition, $namespace, $defaults->getTags());
    }
    public function __destruct()
    {
        parent::__destruct();
        if (isset($this->loader)) {
            $this->loader->registerClasses($this->definition, $this->id, $this->resource, $this->excludes, $this->path);
        }
        unset($this->loader);
    }
    /**
     * @param mixed[]|string $excludes
     * @return static
     */
    final public function exclude($excludes)
    {
        $this->excludes = (array) $excludes;
        return $this;
    }
}
