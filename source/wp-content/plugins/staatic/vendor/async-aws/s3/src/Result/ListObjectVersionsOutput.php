<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use IteratorAggregate;
use Traversable;
use SimpleXMLElement;
use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\EncodingType;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
use Staatic\Vendor\AsyncAws\S3\Input\ListObjectVersionsRequest;
use Staatic\Vendor\AsyncAws\S3\S3Client;
use Staatic\Vendor\AsyncAws\S3\ValueObject\CommonPrefix;
use Staatic\Vendor\AsyncAws\S3\ValueObject\DeleteMarkerEntry;
use Staatic\Vendor\AsyncAws\S3\ValueObject\ObjectVersion;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Owner;
use Staatic\Vendor\AsyncAws\S3\ValueObject\RestoreStatus;
class ListObjectVersionsOutput extends Result implements IteratorAggregate
{
    private $isTruncated;
    private $keyMarker;
    private $versionIdMarker;
    private $nextKeyMarker;
    private $nextVersionIdMarker;
    private $versions;
    private $deleteMarkers;
    private $name;
    private $prefix;
    private $delimiter;
    private $maxKeys;
    private $commonPrefixes;
    private $encodingType;
    private $requestCharged;
    /**
     * @param bool $currentPageOnly
     */
    public function getCommonPrefixes($currentPageOnly = \false): iterable
    {
        if ($currentPageOnly) {
            $this->initialize();
            yield from $this->commonPrefixes;
            return;
        }
        $client = $this->awsClient;
        if (!$client instanceof S3Client) {
            throw new InvalidArgument('missing client injected in paginated result');
        }
        if (!$this->input instanceof ListObjectVersionsRequest) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (\true) {
            $page->initialize();
            if ($page->isTruncated) {
                $input->setKeyMarker($page->nextKeyMarker);
                $input->setVersionIdMarker($page->nextVersionIdMarker);
                $this->registerPrefetch($nextPage = $client->listObjectVersions($input));
            } else {
                $nextPage = null;
            }
            yield from $page->commonPrefixes;
            if (null === $nextPage) {
                break;
            }
            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }
    /**
     * @param bool $currentPageOnly
     */
    public function getDeleteMarkers($currentPageOnly = \false): iterable
    {
        if ($currentPageOnly) {
            $this->initialize();
            yield from $this->deleteMarkers;
            return;
        }
        $client = $this->awsClient;
        if (!$client instanceof S3Client) {
            throw new InvalidArgument('missing client injected in paginated result');
        }
        if (!$this->input instanceof ListObjectVersionsRequest) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (\true) {
            $page->initialize();
            if ($page->isTruncated) {
                $input->setKeyMarker($page->nextKeyMarker);
                $input->setVersionIdMarker($page->nextVersionIdMarker);
                $this->registerPrefetch($nextPage = $client->listObjectVersions($input));
            } else {
                $nextPage = null;
            }
            yield from $page->deleteMarkers;
            if (null === $nextPage) {
                break;
            }
            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }
    public function getDelimiter(): ?string
    {
        $this->initialize();
        return $this->delimiter;
    }
    public function getEncodingType(): ?string
    {
        $this->initialize();
        return $this->encodingType;
    }
    public function getIsTruncated(): ?bool
    {
        $this->initialize();
        return $this->isTruncated;
    }
    public function getIterator(): Traversable
    {
        $client = $this->awsClient;
        if (!$client instanceof S3Client) {
            throw new InvalidArgument('missing client injected in paginated result');
        }
        if (!$this->input instanceof ListObjectVersionsRequest) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (\true) {
            $page->initialize();
            if ($page->isTruncated) {
                $input->setKeyMarker($page->nextKeyMarker);
                $input->setVersionIdMarker($page->nextVersionIdMarker);
                $this->registerPrefetch($nextPage = $client->listObjectVersions($input));
            } else {
                $nextPage = null;
            }
            yield from $page->getVersions(\true);
            yield from $page->getDeleteMarkers(\true);
            yield from $page->getCommonPrefixes(\true);
            if (null === $nextPage) {
                break;
            }
            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }
    public function getKeyMarker(): ?string
    {
        $this->initialize();
        return $this->keyMarker;
    }
    public function getMaxKeys(): ?int
    {
        $this->initialize();
        return $this->maxKeys;
    }
    public function getName(): ?string
    {
        $this->initialize();
        return $this->name;
    }
    public function getNextKeyMarker(): ?string
    {
        $this->initialize();
        return $this->nextKeyMarker;
    }
    public function getNextVersionIdMarker(): ?string
    {
        $this->initialize();
        return $this->nextVersionIdMarker;
    }
    public function getPrefix(): ?string
    {
        $this->initialize();
        return $this->prefix;
    }
    public function getRequestCharged(): ?string
    {
        $this->initialize();
        return $this->requestCharged;
    }
    public function getVersionIdMarker(): ?string
    {
        $this->initialize();
        return $this->versionIdMarker;
    }
    /**
     * @param bool $currentPageOnly
     */
    public function getVersions($currentPageOnly = \false): iterable
    {
        if ($currentPageOnly) {
            $this->initialize();
            yield from $this->versions;
            return;
        }
        $client = $this->awsClient;
        if (!$client instanceof S3Client) {
            throw new InvalidArgument('missing client injected in paginated result');
        }
        if (!$this->input instanceof ListObjectVersionsRequest) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (\true) {
            $page->initialize();
            if ($page->isTruncated) {
                $input->setKeyMarker($page->nextKeyMarker);
                $input->setVersionIdMarker($page->nextVersionIdMarker);
                $this->registerPrefetch($nextPage = $client->listObjectVersions($input));
            } else {
                $nextPage = null;
            }
            yield from $page->versions;
            if (null === $nextPage) {
                break;
            }
            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $headers = $response->getHeaders();
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
        $data = new SimpleXMLElement($response->getContent());
        $this->isTruncated = ($v = $data->IsTruncated) ? filter_var((string) $v, \FILTER_VALIDATE_BOOLEAN) : null;
        $this->keyMarker = ($v = $data->KeyMarker) ? (string) $v : null;
        $this->versionIdMarker = ($v = $data->VersionIdMarker) ? (string) $v : null;
        $this->nextKeyMarker = ($v = $data->NextKeyMarker) ? (string) $v : null;
        $this->nextVersionIdMarker = ($v = $data->NextVersionIdMarker) ? (string) $v : null;
        $this->versions = (!$data->Version) ? [] : $this->populateResultObjectVersionList($data->Version);
        $this->deleteMarkers = (!$data->DeleteMarker) ? [] : $this->populateResultDeleteMarkers($data->DeleteMarker);
        $this->name = ($v = $data->Name) ? (string) $v : null;
        $this->prefix = ($v = $data->Prefix) ? (string) $v : null;
        $this->delimiter = ($v = $data->Delimiter) ? (string) $v : null;
        $this->maxKeys = ($v = $data->MaxKeys) ? (int) (string) $v : null;
        $this->commonPrefixes = (!$data->CommonPrefixes) ? [] : $this->populateResultCommonPrefixList($data->CommonPrefixes);
        $this->encodingType = ($v = $data->EncodingType) ? (string) $v : null;
    }
    private function populateResultChecksumAlgorithmList(SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $a = ($v = $item) ? (string) $v : null;
            if (null !== $a) {
                $items[] = $a;
            }
        }
        return $items;
    }
    private function populateResultCommonPrefixList(SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new CommonPrefix(['Prefix' => ($v = $item->Prefix) ? (string) $v : null]);
        }
        return $items;
    }
    private function populateResultDeleteMarkers(SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new DeleteMarkerEntry(['Owner' => (!$item->Owner) ? null : new Owner(['DisplayName' => ($v = $item->Owner->DisplayName) ? (string) $v : null, 'ID' => ($v = $item->Owner->ID) ? (string) $v : null]), 'Key' => ($v = $item->Key) ? (string) $v : null, 'VersionId' => ($v = $item->VersionId) ? (string) $v : null, 'IsLatest' => ($v = $item->IsLatest) ? filter_var((string) $v, \FILTER_VALIDATE_BOOLEAN) : null, 'LastModified' => ($v = $item->LastModified) ? new DateTimeImmutable((string) $v) : null]);
        }
        return $items;
    }
    private function populateResultObjectVersionList(SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new ObjectVersion(['ETag' => ($v = $item->ETag) ? (string) $v : null, 'ChecksumAlgorithm' => (!$item->ChecksumAlgorithm) ? null : $this->populateResultChecksumAlgorithmList($item->ChecksumAlgorithm), 'Size' => ($v = $item->Size) ? (int) (string) $v : null, 'StorageClass' => ($v = $item->StorageClass) ? (string) $v : null, 'Key' => ($v = $item->Key) ? (string) $v : null, 'VersionId' => ($v = $item->VersionId) ? (string) $v : null, 'IsLatest' => ($v = $item->IsLatest) ? filter_var((string) $v, \FILTER_VALIDATE_BOOLEAN) : null, 'LastModified' => ($v = $item->LastModified) ? new DateTimeImmutable((string) $v) : null, 'Owner' => (!$item->Owner) ? null : new Owner(['DisplayName' => ($v = $item->Owner->DisplayName) ? (string) $v : null, 'ID' => ($v = $item->Owner->ID) ? (string) $v : null]), 'RestoreStatus' => (!$item->RestoreStatus) ? null : new RestoreStatus(['IsRestoreInProgress' => ($v = $item->RestoreStatus->IsRestoreInProgress) ? filter_var((string) $v, \FILTER_VALIDATE_BOOLEAN) : null, 'RestoreExpiryDate' => ($v = $item->RestoreStatus->RestoreExpiryDate) ? new DateTimeImmutable((string) $v) : null])]);
        }
        return $items;
    }
}
