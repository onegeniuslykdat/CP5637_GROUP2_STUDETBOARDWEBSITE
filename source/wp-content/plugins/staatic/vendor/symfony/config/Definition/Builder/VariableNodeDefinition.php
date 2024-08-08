<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use Staatic\Vendor\Symfony\Component\Config\Definition\NodeInterface;
use Staatic\Vendor\Symfony\Component\Config\Definition\VariableNode;
class VariableNodeDefinition extends NodeDefinition
{
    protected function instantiateNode(): VariableNode
    {
        return new VariableNode($this->name, $this->parent, $this->pathSeparator);
    }
    protected function createNode(): NodeInterface
    {
        $node = $this->instantiateNode();
        if (isset($this->normalization)) {
            $node->setNormalizationClosures($this->normalization->before);
        }
        if (isset($this->merge)) {
            $node->setAllowOverwrite($this->merge->allowOverwrite);
        }
        if (\true === $this->default) {
            $node->setDefaultValue($this->defaultValue);
        }
        $node->setAllowEmptyValue($this->allowEmptyValue);
        $node->addEquivalentValue(null, $this->nullEquivalent);
        $node->addEquivalentValue(\true, $this->trueEquivalent);
        $node->addEquivalentValue(\false, $this->falseEquivalent);
        $node->setRequired($this->required);
        if ($this->deprecation) {
            $node->setDeprecated($this->deprecation['package'], $this->deprecation['version'], $this->deprecation['message']);
        }
        if (isset($this->validation)) {
            $node->setFinalValidationClosures($this->validation->rules);
        }
        return $node;
    }
}
