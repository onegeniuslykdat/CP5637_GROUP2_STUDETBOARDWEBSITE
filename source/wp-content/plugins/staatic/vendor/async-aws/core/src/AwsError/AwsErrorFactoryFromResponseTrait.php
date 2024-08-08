<?php

namespace Staatic\Vendor\AsyncAws\Core\AwsError;

use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
trait AwsErrorFactoryFromResponseTrait
{
    /**
     * @param ResponseInterface $response
     */
    public function createFromResponse($response): AwsError
    {
        $content = $response->getContent(\false);
        $headers = $response->getHeaders(\false);
        return $this->createFromContent($content, $headers);
    }
}
