<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute;

use Attribute;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\LogicException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\Expression;
#[Attribute(Attribute::TARGET_PARAMETER)]
class Autowire
{
    /**
     * @readonly
     * @var string|mixed[]|Expression|Reference
     */
    public $value;
    /**
     * @param string|mixed[] $value
     */
    public function __construct($value = null, string $service = null, string $expression = null)
    {
        if (!($service xor $expression xor null !== $value)) {
            throw new LogicException('#[Autowire] attribute must declare exactly one of $service, $expression, or $value.');
        }
        if (\is_string($value) && strncmp($value, '@', strlen('@')) === 0) {
            switch (\true) {
                case strncmp($value, '@@', strlen('@@')) === 0:
                    $value = substr($value, 1);
                    break;
                case strncmp($value, '@=', strlen('@=')) === 0:
                    $expression = substr($value, 2);
                    break;
                default:
                    $service = substr($value, 1);
                    break;
            }
        }
        switch (\true) {
            case null !== $service:
                $this->value = new Reference($service);
                break;
            case null !== $expression:
                if (!class_exists(Expression::class)) {
                    throw new LogicException('Unable to use expressions as the Symfony ExpressionLanguage component is not installed. Try running "composer require symfony/expression-language".');
                }
                $this->value = new Expression($expression);
                break;
            case null !== $value:
                $this->value = $value;
                break;
        }
    }
}
