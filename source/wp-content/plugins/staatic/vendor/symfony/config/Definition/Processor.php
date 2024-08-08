<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

class Processor
{
    /**
     * @param NodeInterface $configTree
     * @param mixed[] $configs
     */
    public function process($configTree, $configs): array
    {
        $currentConfig = [];
        foreach ($configs as $config) {
            $config = $configTree->normalize($config);
            $currentConfig = $configTree->merge($currentConfig, $config);
        }
        return $configTree->finalize($currentConfig);
    }
    /**
     * @param ConfigurationInterface $configuration
     * @param mixed[] $configs
     */
    public function processConfiguration($configuration, $configs): array
    {
        return $this->process($configuration->getConfigTreeBuilder()->buildTree(), $configs);
    }
    /**
     * @param mixed[] $config
     * @param string $key
     * @param string|null $plural
     */
    public static function normalizeConfig($config, $key, $plural = null): array
    {
        $plural = $plural ?? $key . 's';
        if (isset($config[$plural])) {
            return $config[$plural];
        }
        if (isset($config[$key])) {
            if (\is_string($config[$key]) || !\is_int(key($config[$key]))) {
                return [$config[$key]];
            }
            return $config[$key];
        }
        return [];
    }
}
