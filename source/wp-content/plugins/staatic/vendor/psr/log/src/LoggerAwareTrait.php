<?php

namespace Staatic\Vendor\Psr\Log;

trait LoggerAwareTrait
{
    /**
     * @var LoggerInterface|null
     */
    protected $logger;
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger): void
    {
        $this->logger = $logger;
    }
}
