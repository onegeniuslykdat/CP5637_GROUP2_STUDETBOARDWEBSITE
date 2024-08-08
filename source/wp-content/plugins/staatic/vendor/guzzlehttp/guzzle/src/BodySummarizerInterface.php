<?php

namespace Staatic\Vendor\GuzzleHttp;

use Staatic\Vendor\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * @param MessageInterface $message
     */
    public function summarize($message): ?string;
}
