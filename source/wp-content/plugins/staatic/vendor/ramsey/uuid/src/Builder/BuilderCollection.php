<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Builder;

use Staatic\Vendor\Ramsey\Collection\AbstractCollection;
use Staatic\Vendor\Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Staatic\Vendor\Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Staatic\Vendor\Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use Staatic\Vendor\Ramsey\Uuid\Guid\GuidBuilder;
use Staatic\Vendor\Ramsey\Uuid\Math\BrickMathCalculator;
use Staatic\Vendor\Ramsey\Uuid\Nonstandard\UuidBuilder as NonstandardUuidBuilder;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\UuidBuilder as Rfc4122UuidBuilder;
use Traversable;
class BuilderCollection extends AbstractCollection
{
    public function getType(): string
    {
        return UuidBuilderInterface::class;
    }
    public function getIterator(): Traversable
    {
        return parent::getIterator();
    }
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized, ['allowed_classes' => [BrickMathCalculator::class, GenericNumberConverter::class, GenericTimeConverter::class, GuidBuilder::class, NonstandardUuidBuilder::class, PhpTimeConverter::class, Rfc4122UuidBuilder::class]]);
        $this->data = array_filter($data, function ($unserialized): bool {
            return $unserialized instanceof UuidBuilderInterface;
        });
    }
}
