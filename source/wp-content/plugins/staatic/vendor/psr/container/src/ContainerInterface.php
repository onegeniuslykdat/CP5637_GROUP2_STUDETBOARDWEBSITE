<?php

declare (strict_types=1);
namespace Staatic\Vendor\Psr\Container;

interface ContainerInterface
{
    /**
     * @param string $id
     */
    public function get($id);
    /**
     * @param string $id
     */
    public function has($id): bool;
}
