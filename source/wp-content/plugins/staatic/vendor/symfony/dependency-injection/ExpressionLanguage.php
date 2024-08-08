<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use Closure;
use Staatic\Vendor\Psr\Cache\CacheItemPoolInterface;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;
if (!class_exists(BaseExpressionLanguage::class)) {
    return;
}
class ExpressionLanguage extends BaseExpressionLanguage
{
    public function __construct(CacheItemPoolInterface $cache = null, array $providers = [], callable $serviceCompiler = null, Closure $getEnv = null)
    {
        array_unshift($providers, new ExpressionLanguageProvider($serviceCompiler, $getEnv));
        parent::__construct($cache, $providers);
    }
}
