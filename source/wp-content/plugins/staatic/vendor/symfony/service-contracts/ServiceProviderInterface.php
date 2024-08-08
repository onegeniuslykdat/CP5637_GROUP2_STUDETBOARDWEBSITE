<?php

namespace Staatic\Vendor\Symfony\Contracts\Service;

use Staatic\Vendor\Psr\Container\ContainerInterface;
interface ServiceProviderInterface extends ContainerInterface
{
    /**
     * @param string $id
     * @return mixed
     */
    public function get($id);
    /**
     * @param string $id
     */
    public function has($id): bool;
    public function getProvidedServices(): array;
}
