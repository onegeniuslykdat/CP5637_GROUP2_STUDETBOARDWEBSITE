<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

trait ContainerAwareTrait
{
    protected $container;
    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer($container = null)
    {
        if (1 > \func_num_args()) {
            trigger_deprecation('symfony/dependency-injection', '6.2', 'Calling "%s::%s()" without any arguments is deprecated, pass null explicitly instead.', __CLASS__, __FUNCTION__);
        }
        $this->container = $container;
    }
}
