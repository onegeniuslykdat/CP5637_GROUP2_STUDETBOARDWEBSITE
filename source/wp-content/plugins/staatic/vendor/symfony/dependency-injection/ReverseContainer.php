<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use Closure;
use Staatic\Vendor\Psr\Container\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
final class ReverseContainer
{
    /**
     * @var Container
     */
    private $serviceContainer;
    /**
     * @var ContainerInterface
     */
    private $reversibleLocator;
    /**
     * @var string
     */
    private $tagName;
    /**
     * @var Closure
     */
    private $getServiceId;
    public function __construct(Container $serviceContainer, ContainerInterface $reversibleLocator, string $tagName = 'container.reversible')
    {
        $this->serviceContainer = $serviceContainer;
        $this->reversibleLocator = $reversibleLocator;
        $this->tagName = $tagName;
        $this->getServiceId = Closure::bind(function ($service): ?string {
            return (array_search($service, $this->services, \true) ?: array_search($service, $this->privates, \true)) ?: null;
        }, $serviceContainer, Container::class);
    }
    /**
     * @param object $service
     */
    public function getId($service): ?string
    {
        if ($this->serviceContainer === $service) {
            return 'service_container';
        }
        if (null === $id = ($this->getServiceId)($service)) {
            return null;
        }
        if ($this->serviceContainer->has($id) || $this->reversibleLocator->has($id)) {
            return $id;
        }
        return null;
    }
    /**
     * @return object
     */
    public function getService(string $id)
    {
        if ($this->reversibleLocator->has($id)) {
            return $this->reversibleLocator->get($id);
        }
        if (isset($this->serviceContainer->getRemovedIds()[$id])) {
            throw new ServiceNotFoundException($id, null, null, [], sprintf('The "%s" service is private and cannot be accessed by reference. You should either make it public, or tag it as "%s".', $id, $this->tagName));
        }
        return $this->serviceContainer->get($id);
    }
}
