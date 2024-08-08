<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
class CheckCircularReferencesPass implements CompilerPassInterface
{
    /**
     * @var mixed[]
     */
    private $currentPath;
    /**
     * @var mixed[]
     */
    private $checkedNodes;
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        $graph = $container->getCompiler()->getServiceReferenceGraph();
        $this->checkedNodes = [];
        foreach ($graph->getNodes() as $id => $node) {
            $this->currentPath = [$id];
            $this->checkOutEdges($node->getOutEdges());
        }
    }
    private function checkOutEdges(array $edges)
    {
        foreach ($edges as $edge) {
            $node = $edge->getDestNode();
            $id = $node->getId();
            if (empty($this->checkedNodes[$id])) {
                if (!$node->getValue() || !$edge->isLazy() && !$edge->isWeak()) {
                    $searchKey = array_search($id, $this->currentPath);
                    $this->currentPath[] = $id;
                    if (\false !== $searchKey) {
                        throw new ServiceCircularReferenceException($id, \array_slice($this->currentPath, $searchKey));
                    }
                    $this->checkOutEdges($node->getOutEdges());
                }
                $this->checkedNodes[$id] = \true;
                array_pop($this->currentPath);
            }
        }
    }
}
