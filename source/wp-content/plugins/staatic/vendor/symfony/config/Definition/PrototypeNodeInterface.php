<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

interface PrototypeNodeInterface extends NodeInterface
{
    /**
     * @param string $name
     */
    public function setName($name);
}
