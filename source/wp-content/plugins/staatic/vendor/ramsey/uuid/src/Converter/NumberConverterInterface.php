<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Converter;

interface NumberConverterInterface
{
    /**
     * @param string $hex
     */
    public function fromHex($hex): string;
    /**
     * @param string $number
     */
    public function toHex($number): string;
}
