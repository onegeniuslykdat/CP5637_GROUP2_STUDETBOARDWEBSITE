<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension;

use Closure;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Translator;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\XPathExpr;
class AttributeMatchingExtension extends AbstractExtension
{
    public function getAttributeMatchingTranslators(): array
    {
        return ['exists' => Closure::fromCallable([$this, 'translateExists']), '=' => Closure::fromCallable([$this, 'translateEquals']), '~=' => Closure::fromCallable([$this, 'translateIncludes']), '|=' => Closure::fromCallable([$this, 'translateDashMatch']), '^=' => Closure::fromCallable([$this, 'translatePrefixMatch']), '$=' => Closure::fromCallable([$this, 'translateSuffixMatch']), '*=' => Closure::fromCallable([$this, 'translateSubstringMatch']), '!=' => Closure::fromCallable([$this, 'translateDifferent'])];
    }
    /**
     * @param XPathExpr $xpath
     * @param string $attribute
     * @param string|null $value
     */
    public function translateExists($xpath, $attribute, $value): XPathExpr
    {
        return $xpath->addCondition($attribute);
    }
    /**
     * @param XPathExpr $xpath
     * @param string $attribute
     * @param string|null $value
     */
    public function translateEquals($xpath, $attribute, $value): XPathExpr
    {
        return $xpath->addCondition(sprintf('%s = %s', $attribute, Translator::getXpathLiteral($value)));
    }
    /**
     * @param XPathExpr $xpath
     * @param string $attribute
     * @param string|null $value
     */
    public function translateIncludes($xpath, $attribute, $value): XPathExpr
    {
        return $xpath->addCondition($value ? sprintf('%1$s and contains(concat(\' \', normalize-space(%1$s), \' \'), %2$s)', $attribute, Translator::getXpathLiteral(' ' . $value . ' ')) : '0');
    }
    /**
     * @param XPathExpr $xpath
     * @param string $attribute
     * @param string|null $value
     */
    public function translateDashMatch($xpath, $attribute, $value): XPathExpr
    {
        return $xpath->addCondition(sprintf('%1$s and (%1$s = %2$s or starts-with(%1$s, %3$s))', $attribute, Translator::getXpathLiteral($value), Translator::getXpathLiteral($value . '-')));
    }
    /**
     * @param XPathExpr $xpath
     * @param string $attribute
     * @param string|null $value
     */
    public function translatePrefixMatch($xpath, $attribute, $value): XPathExpr
    {
        return $xpath->addCondition($value ? sprintf('%1$s and starts-with(%1$s, %2$s)', $attribute, Translator::getXpathLiteral($value)) : '0');
    }
    /**
     * @param XPathExpr $xpath
     * @param string $attribute
     * @param string|null $value
     */
    public function translateSuffixMatch($xpath, $attribute, $value): XPathExpr
    {
        return $xpath->addCondition($value ? sprintf('%1$s and substring(%1$s, string-length(%1$s)-%2$s) = %3$s', $attribute, \strlen($value) - 1, Translator::getXpathLiteral($value)) : '0');
    }
    /**
     * @param XPathExpr $xpath
     * @param string $attribute
     * @param string|null $value
     */
    public function translateSubstringMatch($xpath, $attribute, $value): XPathExpr
    {
        return $xpath->addCondition($value ? sprintf('%1$s and contains(%1$s, %2$s)', $attribute, Translator::getXpathLiteral($value)) : '0');
    }
    /**
     * @param XPathExpr $xpath
     * @param string $attribute
     * @param string|null $value
     */
    public function translateDifferent($xpath, $attribute, $value): XPathExpr
    {
        return $xpath->addCondition(sprintf($value ? 'not(%1$s) or %1$s != %2$s' : '%s != %s', $attribute, Translator::getXpathLiteral($value)));
    }
    public function getName(): string
    {
        return 'attribute-matching';
    }
}
