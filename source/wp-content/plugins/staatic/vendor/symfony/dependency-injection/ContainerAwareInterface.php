<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

interface ContainerAwareInterface
{
    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer($container);
}
