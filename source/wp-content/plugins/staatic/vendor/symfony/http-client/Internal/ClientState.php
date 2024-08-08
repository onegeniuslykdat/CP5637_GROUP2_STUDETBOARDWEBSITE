<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Internal;

class ClientState
{
    /**
     * @var mixed[]
     */
    public $handlesActivity = [];
    /**
     * @var mixed[]
     */
    public $openHandles = [];
    /**
     * @var float|null
     */
    public $lastTimeout;
}
