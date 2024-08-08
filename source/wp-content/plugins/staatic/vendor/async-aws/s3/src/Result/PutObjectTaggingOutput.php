<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
class PutObjectTaggingOutput extends Result
{
    private $versionId;
    public function getVersionId(): ?string
    {
        $this->initialize();
        return $this->versionId;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $headers = $response->getHeaders();
        $this->versionId = $headers['x-amz-version-id'][0] ?? null;
    }
}
