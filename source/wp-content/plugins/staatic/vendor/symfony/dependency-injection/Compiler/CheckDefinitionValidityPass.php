<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\EnvParameterException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\FileLoader;
class CheckDefinitionValidityPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if ($definition->isSynthetic() && !$definition->isPublic()) {
                throw new RuntimeException(sprintf('A synthetic service ("%s") must be public.', $id));
            }
            if (!$definition->isAbstract() && !$definition->isSynthetic() && !$definition->getClass() && !$definition->hasTag('container.service_locator') && (!$definition->getFactory() || !preg_match(FileLoader::ANONYMOUS_ID_REGEXP, $id))) {
                if ($definition->getFactory()) {
                    throw new RuntimeException(sprintf('Please add the class to service "%s" even if it is constructed by a factory since we might need to add method calls based on compile-time checks.', $id));
                }
                if (class_exists($id) || interface_exists($id, \false)) {
                    if (strncmp($id, '\\', strlen('\\')) === 0 && 1 < substr_count($id, '\\')) {
                        throw new RuntimeException(sprintf('The definition for "%s" has no class attribute, and appears to reference a class or interface. Please specify the class attribute explicitly or remove the leading backslash by renaming the service to "%s" to get rid of this error.', $id, substr($id, 1)));
                    }
                    throw new RuntimeException(sprintf('The definition for "%s" has no class attribute, and appears to reference a class or interface in the global namespace. Leaving out the "class" attribute is only allowed for namespaced classes. Please specify the class attribute explicitly to get rid of this error.', $id));
                }
                throw new RuntimeException(sprintf('The definition for "%s" has no class. If you intend to inject this service dynamically at runtime, please mark it as synthetic=true. If this is an abstract definition solely used by child definitions, please add abstract=true, otherwise specify a class to get rid of this error.', $id));
            }
            foreach ($definition->getTags() as $name => $tags) {
                foreach ($tags as $attributes) {
                    $this->validateAttributes($id, $name, $attributes);
                }
            }
            if ($definition->isPublic() && !$definition->isPrivate()) {
                $resolvedId = $container->resolveEnvPlaceholders($id, null, $usedEnvs);
                if (null !== $usedEnvs) {
                    throw new EnvParameterException([$resolvedId], null, 'A service name ("%s") cannot contain dynamic values.');
                }
            }
        }
        foreach ($container->getAliases() as $id => $alias) {
            if ($alias->isPublic() && !$alias->isPrivate()) {
                $resolvedId = $container->resolveEnvPlaceholders($id, null, $usedEnvs);
                if (null !== $usedEnvs) {
                    throw new EnvParameterException([$resolvedId], null, 'An alias name ("%s") cannot contain dynamic values.');
                }
            }
        }
    }
    private function validateAttributes(string $id, string $tag, array $attributes, array $path = []): void
    {
        foreach ($attributes as $name => $value) {
            if (\is_array($value)) {
                $this->validateAttributes($id, $tag, $value, array_merge($path, [$name]));
            } elseif (!\is_scalar($value) && null !== $value) {
                $name = implode('.', array_merge($path, [$name]));
                throw new RuntimeException(sprintf('A "tags" attribute must be of a scalar-type for service "%s", tag "%s", attribute "%s".', $id, $tag, $name));
            }
        }
    }
}
