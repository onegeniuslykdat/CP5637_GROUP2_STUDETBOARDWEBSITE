<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
use Staatic\Vendor\AsyncAws\S3\ValueObject\DeletedObject;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Error;
class DeleteObjectsOutput extends Result
{
    private $deleted;
    private $requestCharged;
    private $errors;
    public function getDeleted(): array
    {
        $this->initialize();
        return $this->deleted;
    }
    public function getErrors(): array
    {
        $this->initialize();
        return $this->errors;
    }
    public function getRequestCharged(): ?string
    {
        $this->initialize();
        return $this->requestCharged;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $headers = $response->getHeaders();
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
        $data = new SimpleXMLElement($response->getContent());
        $this->deleted = (!$data->Deleted) ? [] : $this->populateResultDeletedObjects($data->Deleted);
        $this->errors = (!$data->Error) ? [] : $this->populateResultErrors($data->Error);
    }
    private function populateResultDeletedObjects(SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new DeletedObject(['Key' => ($v = $item->Key) ? (string) $v : null, 'VersionId' => ($v = $item->VersionId) ? (string) $v : null, 'DeleteMarker' => ($v = $item->DeleteMarker) ? filter_var((string) $v, \FILTER_VALIDATE_BOOLEAN) : null, 'DeleteMarkerVersionId' => ($v = $item->DeleteMarkerVersionId) ? (string) $v : null]);
        }
        return $items;
    }
    private function populateResultErrors(SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new Error(['Key' => ($v = $item->Key) ? (string) $v : null, 'VersionId' => ($v = $item->VersionId) ? (string) $v : null, 'Code' => ($v = $item->Code) ? (string) $v : null, 'Message' => ($v = $item->Message) ? (string) $v : null]);
        }
        return $items;
    }
}
