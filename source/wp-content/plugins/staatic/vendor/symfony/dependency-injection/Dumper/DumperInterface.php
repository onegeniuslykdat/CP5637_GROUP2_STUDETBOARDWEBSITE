<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Dumper;

interface DumperInterface
{
    /**
     * @param mixed[] $options
     * @return mixed[]|string
     */
    public function dump($options = []);
}
