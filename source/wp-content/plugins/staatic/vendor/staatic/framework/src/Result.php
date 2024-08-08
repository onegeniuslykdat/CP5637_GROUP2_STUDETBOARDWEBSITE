<?php

namespace Staatic\Framework;

use DateTimeImmutable;
use DateTimeInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class Result
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $buildId;
    /**
     * @var UriInterface
     */
    private $url;
    /**
     * @var string
     */
    private $urlHash;
    /**
     * @var int
     */
    private $statusCode;
    /**
     * @var string|null
     */
    private $md5;
    /**
     * @var string|null
     */
    private $sha1;
    /**
     * @var int|null
     */
    private $size;
    /**
     * @var string|null
     */
    private $mimeType;
    /**
     * @var string|null
     */
    private $charset;
    /**
     * @var UriInterface|null
     */
    private $redirectUrl;
    /**
     * @var UriInterface|null
     */
    private $originalUrl;
    /**
     * @var UriInterface|null
     */
    private $originalFoundOnUrl;
    /**
     * @var DateTimeInterface
     */
    private $dateCreated;
    public function __construct(string $id, string $buildId, UriInterface $url, string $urlHash, int $statusCode, ?string $md5 = null, ?string $sha1 = null, ?int $size = null, ?string $mimeType = null, ?string $charset = null, ?UriInterface $redirectUrl = null, ?UriInterface $originalUrl = null, ?UriInterface $originalFoundOnUrl = null, ?DateTimeInterface $dateCreated = null)
    {
        $this->id = $id;
        $this->buildId = $buildId;
        $this->url = $url;
        $this->urlHash = $urlHash;
        $this->statusCode = $statusCode;
        $this->md5 = $md5;
        $this->sha1 = $sha1;
        $this->size = $size;
        $this->mimeType = $mimeType;
        $this->charset = $charset;
        $this->redirectUrl = $redirectUrl;
        $this->originalUrl = $originalUrl;
        $this->originalFoundOnUrl = $originalFoundOnUrl;
        $this->dateCreated = $dateCreated ?: new DateTimeImmutable();
    }
    /**
     * @param string $id
     * @param string $buildId
     * @param UriInterface $url
     * @param string $urlHash
     * @param Resource $resource
     * @param mixed[] $properties
     */
    public static function create($id, $buildId, $url, $urlHash, $resource, $properties = [])
    {
        return new self($id, $buildId, $url, $urlHash, $properties['statusCode'] ?? 200, $resource->md5(), $resource->sha1(), $resource->size(), $properties['mimeType'] ?? 'text/html', $properties['charset'] ?? null, $properties['redirectUrl'] ?? null, $properties['originalUrl'] ?? null, $properties['originalFoundOnUrl'] ?? null, $properties['dateCreated'] ?? null);
    }
    /**
     * @param \Staatic\Framework\Result $originalResult
     * @param string $id
     * @param string $buildId
     */
    public static function createFromResult($originalResult, $id, $buildId): self
    {
        $result = clone $originalResult;
        $result->id = $id;
        $result->buildId = $buildId;
        return $result;
    }
    public function __toString()
    {
        return implode(' ~ ', [$this->url, $this->statusCode, $this->mimeType]);
    }
    public function id(): string
    {
        return $this->id;
    }
    public function buildId(): string
    {
        return $this->buildId;
    }
    public function url(): UriInterface
    {
        return $this->url;
    }
    public function urlHash(): string
    {
        return $this->urlHash;
    }
    public function statusCode(): int
    {
        return $this->statusCode;
    }
    public function statusCodeCategory(): int
    {
        return (int) floor($this->statusCode / 100);
    }
    public function md5(): ?string
    {
        return $this->md5;
    }
    public function sha1(): ?string
    {
        return $this->sha1;
    }
    public function size(): ?int
    {
        return $this->size;
    }
    public function mimeType(): ?string
    {
        return $this->mimeType;
    }
    public function charset(): ?string
    {
        return $this->charset;
    }
    public function redirectUrl(): ?UriInterface
    {
        return $this->redirectUrl;
    }
    public function originalUrl(): ?UriInterface
    {
        return $this->originalUrl;
    }
    public function originalFoundOnUrl(): ?UriInterface
    {
        return $this->originalFoundOnUrl;
    }
    public function dateCreated(): DateTimeInterface
    {
        return $this->dateCreated;
    }
    /**
     * @param Resource $resource
     */
    public function syncResource($resource): void
    {
        $this->sha1 = $resource->sha1();
        $this->md5 = $resource->md5();
        $this->size = $resource->size();
    }
}
