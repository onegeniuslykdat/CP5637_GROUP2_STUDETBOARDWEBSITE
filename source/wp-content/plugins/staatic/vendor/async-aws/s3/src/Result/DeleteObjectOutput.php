<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
class DeleteObjectOutput extends Result
{
    private $deleteMarker;
    private $versionId;
    private $requestCharged;
    public function getDeleteMarker(): ?bool
    {
        $this->initialize();
        return $this->deleteMarker;
    }
    public function getRequestCharged(): ?string
    {
        $this->initialize();
        return $this->requestCharged;
    }
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
        $this->deleteMarker = isset($headers['x-amz-delete-marker'][0]) ? filter_var($headers['x-amz-delete-marker'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->versionId = $headers['x-amz-version-id'][0] ?? null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
    }
}
