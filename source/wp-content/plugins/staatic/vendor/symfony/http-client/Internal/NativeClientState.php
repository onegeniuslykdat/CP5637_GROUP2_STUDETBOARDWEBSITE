<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Internal;

final class NativeClientState extends ClientState
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $maxHostConnections = \PHP_INT_MAX;
    /**
     * @var int
     */
    public $responseCount = 0;
    /**
     * @var mixed[]
     */
    public $dnsCache = [];
    /**
     * @var bool
     */
    public $sleep = \false;
    /**
     * @var mixed[]
     */
    public $hosts = [];
    public function __construct()
    {
        $this->id = random_int(\PHP_INT_MIN, \PHP_INT_MAX);
    }
    public function reset()
    {
        $this->responseCount = 0;
        $this->dnsCache = [];
        $this->hosts = [];
    }
}
