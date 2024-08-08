<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Provider;

use Staatic\Vendor\Ramsey\Uuid\Rfc4122\UuidV2;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
interface DceSecurityProviderInterface
{
    public function getUid();
    public function getGid();
}
