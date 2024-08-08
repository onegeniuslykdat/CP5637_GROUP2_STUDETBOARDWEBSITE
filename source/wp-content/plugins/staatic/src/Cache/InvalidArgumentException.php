<?php

declare(strict_types=1);

namespace Staatic\WordPress\Cache;

use Staatic\Vendor\Psr\SimpleCache\InvalidArgumentException as SimpleCacheInvalidArgumentException;

class InvalidArgumentException extends \InvalidArgumentException implements SimpleCacheInvalidArgumentException
{
}
