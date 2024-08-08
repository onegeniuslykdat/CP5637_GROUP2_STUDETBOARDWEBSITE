<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader;

use Closure;
use Staatic\Vendor\Symfony\Component\Config\Loader\Loader;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
class ClosureLoader extends Loader
{
    /**
     * @var ContainerBuilder
     */
    private $container;
    public function __construct(ContainerBuilder $container, string $env = null)
    {
        $this->container = $container;
        parent::__construct($env);
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     * @return mixed
     */
    public function load($resource, $type = null)
    {
        return $resource($this->container, $this->env);
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null): bool
    {
        return $resource instanceof Closure;
    }
}
