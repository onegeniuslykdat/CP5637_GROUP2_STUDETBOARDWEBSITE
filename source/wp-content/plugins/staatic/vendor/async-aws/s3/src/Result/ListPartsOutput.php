<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use IteratorAggregate;
use DateTimeImmutable;
use Traversable;
use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
use Staatic\Vendor\AsyncAws\S3\Enum\StorageClass;
use Staatic\Vendor\AsyncAws\S3\Input\ListPartsRequest;
use Staatic\Vendor\AsyncAws\S3\S3Client;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Initiator;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Owner;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Part;
class ListPartsOutput extends Result implements IteratorAggregate
{
    private $abortDate;
    private $abortRuleId;
    private $bucket;
    private $key;
    private $uploadId;
    private $partNumberMarker;
    private $nextPartNumberMarker;
    private $maxParts;
    private $isTruncated;
    private $parts;
    private $initiator;
    private $owner;
    private $storageClass;
    private $requestCharged;
    private $checksumAlgorithm;
    public function getAbortDate(): ?DateTimeImmutable
    {
        $this->initialize();
        return $this->abortDate;
    }
    public function getAbortRuleId(): ?string
    {
        $this->initialize();
        return $this->abortRuleId;
    }
    public function getBucket(): ?string
    {
        $this->initialize();
        return $this->bucket;
    }
    public function getChecksumAlgorithm(): ?string
    {
        $this->initialize();
        return $this->checksumAlgorithm;
    }
    public function getInitiator(): ?Initiator
    {
        $this->initialize();
        return $this->initiator;
    }
    public function getIsTruncated(): ?bool
    {
        $this->initialize();
        return $this->isTruncated;
    }
    public function getIterator(): Traversable
    {
        yield from $this->getParts();
    }
    public function getKey(): ?string
    {
        $this->initialize();
        return $this->key;
    }
    public function getMaxParts(): ?int
    {
        $this->initialize();
        return $this->maxParts;
    }
    public function getNextPartNumberMarker(): ?int
    {
        $this->initialize();
        return $this->nextPartNumberMarker;
    }
    public function getOwner(): ?Owner
    {
        $this->initialize();
        return $this->owner;
    }
    public function getPartNumberMarker(): ?int
    {
        $this->initialize();
        return $this->partNumberMarker;
    }
    /**
     * @param bool $currentPageOnly
     */
    public function getParts($currentPageOnly = \false): iterable
    {
        if ($currentPageOnly) {
            $this->initialize();
            yield from $this->parts;
            return;
        }
        $client = $this->awsClient;
        if (!$client instanceof S3Client) {
            throw new InvalidArgument('missing client injected in paginated result');
        }
        if (!$this->input instanceof ListPartsRequest) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (\true) {
            $page->initialize();
            if ($page->isTruncated) {
                $input->setPartNumberMarker($page->nextPartNumberMarker);
                $this->registerPrefetch($nextPage = $client->listParts($input));
            } else {
                $nextPage = null;
            }
            yield from $page->parts;
            if (null === $nextPage) {
                break;
            }
            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }
    public function getRequestCharged(): ?string
    {
        $this->initialize();
        return $this->requestCharged;
    }
    public function getStorageClass(): ?string
    {
        $this->initialize();
        return $this->storageClass;
    }
    public function getUploadId(): ?string
    {
        $this->initialize();
        return $this->uploadId;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $headers = $response->getHeaders();
        $this->abortDate = isset($headers['x-amz-abort-date'][0]) ? new DateTimeImmutable($headers['x-amz-abort-date'][0]) : null;
        $this->abortRuleId = $headers['x-amz-abort-rule-id'][0] ?? null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
        $data = new SimpleXMLElement($response->getContent());
        $this->bucket = ($v = $data->Bucket) ? (string) $v : null;
        $this->key = ($v = $data->Key) ? (string) $v : null;
        $this->uploadId = ($v = $data->UploadId) ? (string) $v : null;
        $this->partNumberMarker = ($v = $data->PartNumberMarker) ? (int) (string) $v : null;
        $this->nextPartNumberMarker = ($v = $data->NextPartNumberMarker) ? (int) (string) $v : null;
        $this->maxParts = ($v = $data->MaxParts) ? (int) (string) $v : null;
        $this->isTruncated = ($v = $data->IsTruncated) ? filter_var((string) $v, \FILTER_VALIDATE_BOOLEAN) : null;
        $this->parts = (!$data->Part) ? [] : $this->populateResultParts($data->Part);
        $this->initiator = (!$data->Initiator) ? null : new Initiator(['ID' => ($v = $data->Initiator->ID) ? (string) $v : null, 'DisplayName' => ($v = $data->Initiator->DisplayName) ? (string) $v : null]);
        $this->owner = (!$data->Owner) ? null : new Owner(['DisplayName' => ($v = $data->Owner->DisplayName) ? (string) $v : null, 'ID' => ($v = $data->Owner->ID) ? (string) $v : null]);
        $this->storageClass = ($v = $data->StorageClass) ? (string) $v : null;
        $this->checksumAlgorithm = ($v = $data->ChecksumAlgorithm) ? (string) $v : null;
    }
    private function populateResultParts(SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new Part(['PartNumber' => ($v = $item->PartNumber) ? (int) (string) $v : null, 'LastModified' => ($v = $item->LastModified) ? new DateTimeImmutable((string) $v) : null, 'ETag' => ($v = $item->ETag) ? (string) $v : null, 'Size' => ($v = $item->Size) ? (int) (string) $v : null, 'ChecksumCRC32' => ($v = $item->ChecksumCRC32) ? (string) $v : null, 'ChecksumCRC32C' => ($v = $item->ChecksumCRC32C) ? (string) $v : null, 'ChecksumSHA1' => ($v = $item->ChecksumSHA1) ? (string) $v : null, 'ChecksumSHA256' => ($v = $item->ChecksumSHA256) ? (string) $v : null]);
        }
        return $items;
    }
}
