<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

interface TaggedContainerInterface extends ContainerInterface
{
    /**
     * @param string $name
     */
    public function findTaggedServiceIds($name): array;
}
