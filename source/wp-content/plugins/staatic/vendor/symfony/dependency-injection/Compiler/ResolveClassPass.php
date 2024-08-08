<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ChildDefinition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
class ResolveClassPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if ($definition->isSynthetic() || null !== $definition->getClass()) {
                continue;
            }
            if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+(?:\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+)++$/', $id)) {
                if ($definition instanceof ChildDefinition && !class_exists($id)) {
                    throw new InvalidArgumentException(sprintf('Service definition "%s" has a parent but no class, and its name looks like an FQCN. Either the class is missing or you want to inherit it from the parent service. To resolve this ambiguity, please rename this service to a non-FQCN (e.g. using dots), or create the missing class.', $id));
                }
                $definition->setClass($id);
            }
        }
    }
}
