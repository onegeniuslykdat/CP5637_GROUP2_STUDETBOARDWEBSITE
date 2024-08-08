<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Provider\Time;

use Staatic\Vendor\Ramsey\Uuid\Provider\TimeProviderInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Time;
use function gettimeofday;
class SystemTimeProvider implements TimeProviderInterface
{
    public function getTime(): Time
    {
        $time = gettimeofday();
        return new Time($time['sec'], $time['usec']);
    }
}
