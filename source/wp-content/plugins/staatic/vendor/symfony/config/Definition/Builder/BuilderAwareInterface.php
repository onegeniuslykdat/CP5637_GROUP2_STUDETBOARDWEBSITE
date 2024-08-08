<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

interface BuilderAwareInterface
{
    /**
     * @param NodeBuilder $builder
     */
    public function setBuilder($builder);
}
