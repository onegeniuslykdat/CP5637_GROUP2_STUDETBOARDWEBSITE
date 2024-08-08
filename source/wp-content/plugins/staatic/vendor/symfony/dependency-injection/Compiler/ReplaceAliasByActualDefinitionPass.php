<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class ReplaceAliasByActualDefinitionPass extends AbstractRecursivePass
{
    /**
     * @var mixed[]
     */
    private $replacements;
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        $seenAliasTargets = [];
        $replacements = [];
        foreach ($container->getAliases() as $definitionId => $target) {
            $targetId = (string) $target;
            if ('service_container' === $targetId) {
                continue;
            }
            if (isset($replacements[$targetId])) {
                $container->setAlias($definitionId, $replacements[$targetId])->setPublic($target->isPublic());
                if ($target->isDeprecated()) {
                    $container->getAlias($definitionId)->setDeprecated(...array_values($target->getDeprecation('%alias_id%')));
                }
            }
            if (isset($seenAliasTargets[$targetId])) {
                continue;
            }
            $seenAliasTargets[$targetId] = \true;
            try {
                $definition = $container->getDefinition($targetId);
            } catch (ServiceNotFoundException $e) {
                if ('' !== $e->getId() && '@' === $e->getId()[0]) {
                    throw new ServiceNotFoundException($e->getId(), $e->getSourceId(), null, [substr($e->getId(), 1)]);
                }
                throw $e;
            }
            if ($definition->isPublic()) {
                continue;
            }
            $definition->setPublic($target->isPublic());
            $container->setDefinition($definitionId, $definition);
            $container->removeDefinition($targetId);
            $replacements[$targetId] = $definitionId;
            if ($target->isPublic() && $target->isDeprecated()) {
                $definition->addTag('container.private', $target->getDeprecation('%service_id%'));
            }
        }
        $this->replacements = $replacements;
        parent::process($container);
        $this->replacements = [];
    }
    /**
     * @param mixed $value
     * @param bool $isRoot
     * @return mixed
     */
    protected function processValue($value, $isRoot = \false)
    {
        if ($value instanceof Reference && isset($this->replacements[$referenceId = (string) $value])) {
            $newId = $this->replacements[$referenceId];
            $value = new Reference($newId, $value->getInvalidBehavior());
            $this->container->log($this, sprintf('Changed reference of service "%s" previously pointing to "%s" to "%s".', $this->currentId, $referenceId, $newId));
        }
        return parent::processValue($value, $isRoot);
    }
}
