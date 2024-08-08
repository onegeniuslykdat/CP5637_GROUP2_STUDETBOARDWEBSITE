<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid;

use DateTimeInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
function v1($node = null, ?int $clockSeq = null): string
{
    return Uuid::uuid1($node, $clockSeq)->toString();
}
function v2(int $localDomain, ?IntegerObject $localIdentifier = null, ?Hexadecimal $node = null, ?int $clockSeq = null): string
{
    return Uuid::uuid2($localDomain, $localIdentifier, $node, $clockSeq)->toString();
}
function v3($ns, string $name): string
{
    return Uuid::uuid3($ns, $name)->toString();
}
function v4(): string
{
    return Uuid::uuid4()->toString();
}
function v5($ns, string $name): string
{
    return Uuid::uuid5($ns, $name)->toString();
}
function v6(?Hexadecimal $node = null, ?int $clockSeq = null): string
{
    return Uuid::uuid6($node, $clockSeq)->toString();
}
function v7(?DateTimeInterface $dateTime = null): string
{
    return Uuid::uuid7($dateTime)->toString();
}
function v8(string $bytes): string
{
    return Uuid::uuid8($bytes)->toString();
}
