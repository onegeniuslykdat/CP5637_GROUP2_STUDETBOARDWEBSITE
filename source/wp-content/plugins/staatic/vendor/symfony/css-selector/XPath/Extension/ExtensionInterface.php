<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension;

interface ExtensionInterface
{
    public function getNodeTranslators(): array;
    public function getCombinationTranslators(): array;
    public function getFunctionTranslators(): array;
    public function getPseudoClassTranslators(): array;
    public function getAttributeMatchingTranslators(): array;
    public function getName(): string;
}
