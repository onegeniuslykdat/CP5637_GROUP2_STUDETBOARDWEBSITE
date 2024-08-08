<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension;

use Closure;
use Staatic\Vendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException;
use Staatic\Vendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\FunctionNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Parser;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Translator;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\XPathExpr;
class FunctionExtension extends AbstractExtension
{
    public function getFunctionTranslators(): array
    {
        return ['nth-child' => Closure::fromCallable([$this, 'translateNthChild']), 'nth-last-child' => Closure::fromCallable([$this, 'translateNthLastChild']), 'nth-of-type' => Closure::fromCallable([$this, 'translateNthOfType']), 'nth-last-of-type' => Closure::fromCallable([$this, 'translateNthLastOfType']), 'contains' => Closure::fromCallable([$this, 'translateContains']), 'lang' => Closure::fromCallable([$this, 'translateLang'])];
    }
    /**
     * @param XPathExpr $xpath
     * @param FunctionNode $function
     * @param bool $last
     * @param bool $addNameTest
     */
    public function translateNthChild($xpath, $function, $last = \false, $addNameTest = \true): XPathExpr
    {
        try {
            [$a, $b] = Parser::parseSeries($function->getArguments());
        } catch (SyntaxErrorException $e) {
            throw new ExpressionErrorException(sprintf('Invalid series: "%s".', implode('", "', $function->getArguments())), 0, $e);
        }
        $xpath->addStarPrefix();
        if ($addNameTest) {
            $xpath->addNameTest();
        }
        if (0 === $a) {
            return $xpath->addCondition('position() = ' . ($last ? 'last() - ' . ($b - 1) : $b));
        }
        if ($a < 0) {
            if ($b < 1) {
                return $xpath->addCondition('false()');
            }
            $sign = '<=';
        } else {
            $sign = '>=';
        }
        $expr = 'position()';
        if ($last) {
            $expr = 'last() - ' . $expr;
            --$b;
        }
        if (0 !== $b) {
            $expr .= ' - ' . $b;
        }
        $conditions = [sprintf('%s %s 0', $expr, $sign)];
        if (1 !== $a && -1 !== $a) {
            $conditions[] = sprintf('(%s) mod %d = 0', $expr, $a);
        }
        return $xpath->addCondition(implode(' and ', $conditions));
    }
    /**
     * @param XPathExpr $xpath
     * @param FunctionNode $function
     */
    public function translateNthLastChild($xpath, $function): XPathExpr
    {
        return $this->translateNthChild($xpath, $function, \true);
    }
    /**
     * @param XPathExpr $xpath
     * @param FunctionNode $function
     */
    public function translateNthOfType($xpath, $function): XPathExpr
    {
        return $this->translateNthChild($xpath, $function, \false, \false);
    }
    /**
     * @param XPathExpr $xpath
     * @param FunctionNode $function
     */
    public function translateNthLastOfType($xpath, $function): XPathExpr
    {
        if ('*' === $xpath->getElement()) {
            throw new ExpressionErrorException('"*:nth-of-type()" is not implemented.');
        }
        return $this->translateNthChild($xpath, $function, \true, \false);
    }
    /**
     * @param XPathExpr $xpath
     * @param FunctionNode $function
     */
    public function translateContains($xpath, $function): XPathExpr
    {
        $arguments = $function->getArguments();
        foreach ($arguments as $token) {
            if (!($token->isString() || $token->isIdentifier())) {
                throw new ExpressionErrorException('Expected a single string or identifier for :contains(), got ' . implode(', ', $arguments));
            }
        }
        return $xpath->addCondition(sprintf('contains(string(.), %s)', Translator::getXpathLiteral($arguments[0]->getValue())));
    }
    /**
     * @param XPathExpr $xpath
     * @param FunctionNode $function
     */
    public function translateLang($xpath, $function): XPathExpr
    {
        $arguments = $function->getArguments();
        foreach ($arguments as $token) {
            if (!($token->isString() || $token->isIdentifier())) {
                throw new ExpressionErrorException('Expected a single string or identifier for :lang(), got ' . implode(', ', $arguments));
            }
        }
        return $xpath->addCondition(sprintf('lang(%s)', Translator::getXpathLiteral($arguments[0]->getValue())));
    }
    public function getName(): string
    {
        return 'function';
    }
}
