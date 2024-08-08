<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Internal;

final class DnsCache
{
    /**
     * @var mixed[]
     */
    public $hostnames = [];
    /**
     * @var mixed[]
     */
    public $removals = [];
    /**
     * @var mixed[]
     */
    public $evictions = [];
}
