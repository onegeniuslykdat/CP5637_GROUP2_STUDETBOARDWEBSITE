<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag;

use Staatic\Vendor\Psr\Container\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
interface ContainerBagInterface extends ContainerInterface
{
    public function all(): array;
    /**
     * @param mixed $value
     */
    public function resolveValue($value);
    /**
     * @param mixed $value
     * @return mixed
     */
    public function escapeValue($value);
    /**
     * @param mixed $value
     * @return mixed
     */
    public function unescapeValue($value);
}
