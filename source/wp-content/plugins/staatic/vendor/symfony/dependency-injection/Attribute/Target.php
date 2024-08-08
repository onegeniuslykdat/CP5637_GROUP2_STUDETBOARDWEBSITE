<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute;

use Attribute;
use ReflectionParameter;
use ReflectionMethod;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
#[Attribute(Attribute::TARGET_PARAMETER)]
final class Target
{
    /**
     * @var string
     */
    public $name;
    public function __construct(string $name)
    {
        $this->name = lcfirst(str_replace(' ', '', ucwords(preg_replace('/[^a-zA-Z0-9\x7f-\xff]++/', ' ', $name))));
    }
    public static function parseName(ReflectionParameter $parameter): string
    {
        if (!$target = (method_exists($parameter, 'getAttributes') ? $parameter->getAttributes(self::class) : [])[0] ?? null) {
            return $parameter->name;
        }
        $name = $target->newInstance()->name;
        if (!preg_match('/^[a-zA-Z_\x7f-\xff]/', $name)) {
            if (($function = $parameter->getDeclaringFunction()) instanceof ReflectionMethod) {
                $function = $function->class . '::' . $function->name;
            } else {
                $function = $function->name;
            }
            throw new InvalidArgumentException(sprintf('Invalid #[Target] name "%s" on parameter "$%s" of "%s()": the first character must be a letter.', $name, $parameter->name, $function));
        }
        return $name;
    }
}
