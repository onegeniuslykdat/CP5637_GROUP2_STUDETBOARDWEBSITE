<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use Closure;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\LogicException;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @var Closure|null
     */
    private $serviceCompiler;
    /**
     * @var Closure|null
     */
    private $getEnv;
    public function __construct(callable $serviceCompiler = null, Closure $getEnv = null)
    {
        $this->serviceCompiler = (null === $serviceCompiler) ? null : Closure::fromCallable($serviceCompiler);
        $this->getEnv = $getEnv;
    }
    public function getFunctions(): array
    {
        return [new ExpressionFunction('service', $this->serviceCompiler ?? function ($arg) {
            return sprintf('$this->get(%s)', $arg);
        }, function (array $variables, $value) {
            return $variables['container']->get($value);
        }), new ExpressionFunction('parameter', function ($arg) {
            return sprintf('$this->getParameter(%s)', $arg);
        }, function (array $variables, $value) {
            return $variables['container']->getParameter($value);
        }), new ExpressionFunction('env', function ($arg) {
            return sprintf('$this->getEnv(%s)', $arg);
        }, function (array $variables, $value) {
            if (!$this->getEnv) {
                throw new LogicException('You need to pass a getEnv closure to the expression langage provider to use the "env" function.');
            }
            return ($this->getEnv)($value);
        }), new ExpressionFunction('arg', function ($arg) {
            return sprintf('$args?->get(%s)', $arg);
        }, function (array $variables, $value) {
            return ($nullsafeVariable1 = $variables['args']) ? $nullsafeVariable1->get($value) : null;
        })];
    }
}
