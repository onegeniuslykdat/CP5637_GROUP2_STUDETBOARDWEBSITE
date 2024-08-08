<?php

namespace Staatic\Vendor\AsyncAws\Core\AwsError;

use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
interface AwsErrorFactoryInterface
{
    /**
     * @param ResponseInterface $response
     */
    public function createFromResponse($response): AwsError;
    /**
     * @param string $content
     * @param mixed[] $headers
     */
    public function createFromContent($content, $headers): AwsError;
}
