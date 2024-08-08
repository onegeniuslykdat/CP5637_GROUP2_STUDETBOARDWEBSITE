<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
class CreateBucketOutput extends Result
{
    private $location;
    public function getLocation(): ?string
    {
        $this->initialize();
        return $this->location;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $headers = $response->getHeaders();
        $this->location = $headers['location'][0] ?? null;
    }
}
