<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Rfc4122;

use Staatic\Vendor\Ramsey\Uuid\Uuid;
trait VersionTrait
{
    abstract public function getVersion(): ?int;
    abstract public function isMax(): bool;
    abstract public function isNil(): bool;
    private function isCorrectVersion(): bool
    {
        if ($this->isNil() || $this->isMax()) {
            return \true;
        }
        switch ($this->getVersion()) {
            case Uuid::UUID_TYPE_TIME:
            case Uuid::UUID_TYPE_DCE_SECURITY:
            case Uuid::UUID_TYPE_HASH_MD5:
            case Uuid::UUID_TYPE_RANDOM:
            case Uuid::UUID_TYPE_HASH_SHA1:
            case Uuid::UUID_TYPE_REORDERED_TIME:
            case Uuid::UUID_TYPE_UNIX_TIME:
            case Uuid::UUID_TYPE_CUSTOM:
                return \true;
            default:
                return \false;
        }
    }
}
