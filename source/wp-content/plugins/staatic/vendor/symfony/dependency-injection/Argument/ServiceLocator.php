<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

use Closure;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator as BaseServiceLocator;
class ServiceLocator extends BaseServiceLocator
{
    /**
     * @var Closure
     */
    private $factory;
    /**
     * @var mixed[]
     */
    private $serviceMap;
    /**
     * @var mixed[]|null
     */
    private $serviceTypes;
    public function __construct(Closure $factory, array $serviceMap, array $serviceTypes = null)
    {
        $this->factory = $factory;
        $this->serviceMap = $serviceMap;
        $this->serviceTypes = $serviceTypes;
        parent::__construct($serviceMap);
    }
    /**
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        switch (\count($this->serviceMap[$id] ?? [])) {
            case 0:
                return parent::get($id);
            case 1:
                return $this->serviceMap[$id][0];
            default:
                return ($this->factory)(...$this->serviceMap[$id]);
        }
    }
    public function getProvidedServices(): array
    {
        return $this->serviceTypes = $this->serviceTypes ?? array_map(function () {
            return '?';
        }, $this->serviceMap);
    }
}
