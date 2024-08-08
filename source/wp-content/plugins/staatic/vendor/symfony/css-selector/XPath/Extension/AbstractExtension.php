<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension;

abstract class AbstractExtension implements ExtensionInterface
{
    public function getNodeTranslators(): array
    {
        return [];
    }
    public function getCombinationTranslators(): array
    {
        return [];
    }
    public function getFunctionTranslators(): array
    {
        return [];
    }
    public function getPseudoClassTranslators(): array
    {
        return [];
    }
    public function getAttributeMatchingTranslators(): array
    {
        return [];
    }
}
