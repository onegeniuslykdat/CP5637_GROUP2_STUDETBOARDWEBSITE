<?php

namespace Staatic\Vendor\Symfony\Contracts\Service;

use Staatic\Vendor\Symfony\Contracts\Service\Attribute\SubscribedService;
interface ServiceSubscriberInterface
{
    public static function getSubscribedServices(): array;
}
