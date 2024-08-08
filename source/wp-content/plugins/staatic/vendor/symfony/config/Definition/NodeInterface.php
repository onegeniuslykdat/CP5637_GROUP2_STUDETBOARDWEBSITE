<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidTypeException;
interface NodeInterface
{
    public function getName(): string;
    public function getPath(): string;
    public function isRequired(): bool;
    public function hasDefaultValue(): bool;
    /**
     * @return mixed
     */
    public function getDefaultValue();
    /**
     * @param mixed $value
     * @return mixed
     */
    public function normalize($value);
    /**
     * @param mixed $leftSide
     * @param mixed $rightSide
     * @return mixed
     */
    public function merge($leftSide, $rightSide);
    /**
     * @param mixed $value
     * @return mixed
     */
    public function finalize($value);
}
