<?php

namespace Staatic\Vendor\Symfony\Component\Config\Loader;

class LoaderResolver implements LoaderResolverInterface
{
    /**
     * @var mixed[]
     */
    private $loaders = [];
    public function __construct(array $loaders = [])
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }
    /**
     * @param mixed $resource
     * @param string|null $type
     * @return LoaderInterface|false
     */
    public function resolve($resource, $type = null)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($resource, $type)) {
                return $loader;
            }
        }
        return \false;
    }
    /**
     * @param LoaderInterface $loader
     */
    public function addLoader($loader)
    {
        $this->loaders[] = $loader;
        $loader->setResolver($this);
    }
    public function getLoaders(): array
    {
        return $this->loaders;
    }
}
