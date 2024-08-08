<?php

namespace Staatic\Vendor\Symfony\Component\Config\Builder;

interface ConfigBuilderInterface
{
    public function toArray(): array;
    public function getExtensionAlias(): string;
}
