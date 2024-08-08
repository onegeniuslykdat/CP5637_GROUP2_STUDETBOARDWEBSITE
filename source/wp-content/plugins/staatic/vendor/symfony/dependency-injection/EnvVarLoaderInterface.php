<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

interface EnvVarLoaderInterface
{
    public function loadEnvVars(): array;
}
