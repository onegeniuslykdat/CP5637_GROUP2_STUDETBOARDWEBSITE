<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class CheckExceptionOnInvalidReferenceBehaviorPass extends AbstractRecursivePass
{
    /**
     * @var mixed[]
     */
    private $serviceLocatorContextIds = [];
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        $this->serviceLocatorContextIds = [];
        foreach ($container->findTaggedServiceIds('container.service_locator_context') as $id => $tags) {
            $this->serviceLocatorContextIds[$id] = $tags[0]['id'];
            $container->getDefinition($id)->clearTag('container.service_locator_context');
        }
        try {
            return parent::process($container);
        } finally {
            $this->serviceLocatorContextIds = [];
        }
    }
    /**
     * @param mixed $value
     * @param bool $isRoot
     * @return mixed
     */
    protected function processValue($value, $isRoot = \false)
    {
        if (!$value instanceof Reference) {
            return parent::processValue($value, $isRoot);
        }
        if (ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE < $value->getInvalidBehavior() || $this->container->has($id = (string) $value)) {
            return $value;
        }
        $currentId = $this->currentId;
        $graph = $this->container->getCompiler()->getServiceReferenceGraph();
        if (isset($this->serviceLocatorContextIds[$currentId])) {
            $currentId = $this->serviceLocatorContextIds[$currentId];
            $locator = $this->container->getDefinition($this->currentId)->getFactory()[0];
            foreach ($locator->getArgument(0) as $k => $v) {
                if ($v->getValues()[0] === $value) {
                    if ($k !== $id) {
                        $currentId = $k . '" in the container provided to "' . $currentId;
                    }
                    throw new ServiceNotFoundException($id, $currentId, null, $this->getAlternatives($id));
                }
            }
        }
        if ('.' === $currentId[0] && $graph->hasNode($currentId)) {
            foreach ($graph->getNode($currentId)->getInEdges() as $edge) {
                if (!$edge->getValue() instanceof Reference || ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE < $edge->getValue()->getInvalidBehavior()) {
                    continue;
                }
                $sourceId = $edge->getSourceNode()->getId();
                if ('.' !== $sourceId[0]) {
                    $currentId = $sourceId;
                    break;
                }
            }
        }
        throw new ServiceNotFoundException($id, $currentId, null, $this->getAlternatives($id));
    }
    private function getAlternatives(string $id): array
    {
        $alternatives = [];
        foreach ($this->container->getServiceIds() as $knownId) {
            if ('' === $knownId || '.' === $knownId[0]) {
                continue;
            }
            $lev = levenshtein($id, $knownId);
            if ($lev <= \strlen($id) / 3 || strpos($knownId, $id) !== false) {
                $alternatives[] = $knownId;
            }
        }
        return $alternatives;
    }
}
