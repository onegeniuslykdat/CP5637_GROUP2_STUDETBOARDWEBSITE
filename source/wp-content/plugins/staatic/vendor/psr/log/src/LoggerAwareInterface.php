<?php

namespace Staatic\Vendor\Psr\Log;

interface LoggerAwareInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger): void;
}
