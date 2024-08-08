<?php

namespace Staatic\Vendor\Symfony\Contracts\Service\Attribute;

use Attribute;
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
final class Required
{
}
