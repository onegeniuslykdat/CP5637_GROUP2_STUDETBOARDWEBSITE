<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use UnitEnum;
use Staatic\Vendor\Psr\Container\ContainerInterface as PsrContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
interface ContainerInterface extends PsrContainerInterface
{
    public const RUNTIME_EXCEPTION_ON_INVALID_REFERENCE = 0;
    public const EXCEPTION_ON_INVALID_REFERENCE = 1;
    public const NULL_ON_INVALID_REFERENCE = 2;
    public const IGNORE_ON_INVALID_REFERENCE = 3;
    public const IGNORE_ON_UNINITIALIZED_REFERENCE = 4;
    /**
     * @param string $id
     * @param object|null $service
     */
    public function set($id, $service);
    /**
     * @param string $id
     * @param int $invalidBehavior
     */
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE);
    /**
     * @param string $id
     */
    public function has($id): bool;
    /**
     * @param string $id
     */
    public function initialized($id): bool;
    /**
     * @param string $name
     */
    public function getParameter($name);
    /**
     * @param string $name
     */
    public function hasParameter($name): bool;
    /**
     * @param string $name
     * @param mixed[]|bool|string|int|float|UnitEnum|null $value
     */
    public function setParameter($name, $value);
}
