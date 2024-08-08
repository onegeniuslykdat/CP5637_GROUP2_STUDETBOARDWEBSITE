<?php

namespace Staatic\Vendor\Symfony\Contracts\Service;

use Countable;
use IteratorAggregate;
interface ServiceCollectionInterface extends ServiceProviderInterface, Countable, IteratorAggregate
{
}
