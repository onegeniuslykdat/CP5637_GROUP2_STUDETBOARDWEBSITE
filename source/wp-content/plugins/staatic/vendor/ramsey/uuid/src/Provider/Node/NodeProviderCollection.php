<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Provider\Node;

use Staatic\Vendor\Ramsey\Collection\AbstractCollection;
use Staatic\Vendor\Ramsey\Uuid\Provider\NodeProviderInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
class NodeProviderCollection extends AbstractCollection
{
    public function getType(): string
    {
        return NodeProviderInterface::class;
    }
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized, ['allowed_classes' => [Hexadecimal::class, RandomNodeProvider::class, StaticNodeProvider::class, SystemNodeProvider::class]]);
        $this->data = array_filter($data, function ($unserialized): bool {
            return $unserialized instanceof NodeProviderInterface;
        });
    }
}
