<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

use InvalidArgumentException;
use Staatic\Vendor\Psr\Http\Message\MessageInterface;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
trait MessageTrait
{
    private $headers = [];
    private $headerNames = [];
    private $protocol = '1.1';
    private $stream;
    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }
    public function withProtocolVersion($version): MessageInterface
    {
        if ($this->protocol === $version) {
            return $this;
        }
        $new = clone $this;
        $new->protocol = $version;
        return $new;
    }
    public function getHeaders(): array
    {
        return $this->headers;
    }
    public function hasHeader($header): bool
    {
        return isset($this->headerNames[strtolower($header)]);
    }
    public function getHeader($header): array
    {
        $header = strtolower($header);
        if (!isset($this->headerNames[$header])) {
            return [];
        }
        $header = $this->headerNames[$header];
        return $this->headers[$header];
    }
    public function getHeaderLine($header): string
    {
        return implode(', ', $this->getHeader($header));
    }
    public function withHeader($header, $value): MessageInterface
    {
        $this->assertHeader($header);
        $value = $this->normalizeHeaderValue($value);
        $normalized = strtolower($header);
        $new = clone $this;
        if (isset($new->headerNames[$normalized])) {
            unset($new->headers[$new->headerNames[$normalized]]);
        }
        $new->headerNames[$normalized] = $header;
        $new->headers[$header] = $value;
        return $new;
    }
    public function withAddedHeader($header, $value): MessageInterface
    {
        $this->assertHeader($header);
        $value = $this->normalizeHeaderValue($value);
        $normalized = strtolower($header);
        $new = clone $this;
        if (isset($new->headerNames[$normalized])) {
            $header = $this->headerNames[$normalized];
            $new->headers[$header] = array_merge($this->headers[$header], $value);
        } else {
            $new->headerNames[$normalized] = $header;
            $new->headers[$header] = $value;
        }
        return $new;
    }
    public function withoutHeader($header): MessageInterface
    {
        $normalized = strtolower($header);
        if (!isset($this->headerNames[$normalized])) {
            return $this;
        }
        $header = $this->headerNames[$normalized];
        $new = clone $this;
        unset($new->headers[$header], $new->headerNames[$normalized]);
        return $new;
    }
    public function getBody(): StreamInterface
    {
        if (!$this->stream) {
            $this->stream = Utils::streamFor('');
        }
        return $this->stream;
    }
    /**
     * @param StreamInterface $body
     */
    public function withBody($body): MessageInterface
    {
        if ($body === $this->stream) {
            return $this;
        }
        $new = clone $this;
        $new->stream = $body;
        return $new;
    }
    private function setHeaders(array $headers): void
    {
        $this->headerNames = $this->headers = [];
        foreach ($headers as $header => $value) {
            $header = (string) $header;
            $this->assertHeader($header);
            $value = $this->normalizeHeaderValue($value);
            $normalized = strtolower($header);
            if (isset($this->headerNames[$normalized])) {
                $header = $this->headerNames[$normalized];
                $this->headers[$header] = array_merge($this->headers[$header], $value);
            } else {
                $this->headerNames[$normalized] = $header;
                $this->headers[$header] = $value;
            }
        }
    }
    private function normalizeHeaderValue($value): array
    {
        if (!is_array($value)) {
            return $this->trimAndValidateHeaderValues([$value]);
        }
        if (count($value) === 0) {
            throw new InvalidArgumentException('Header value can not be an empty array.');
        }
        return $this->trimAndValidateHeaderValues($value);
    }
    private function trimAndValidateHeaderValues(array $values): array
    {
        return array_map(function ($value) {
            if (!is_scalar($value) && null !== $value) {
                throw new InvalidArgumentException(sprintf('Header value must be scalar or null but %s provided.', is_object($value) ? get_class($value) : gettype($value)));
            }
            $trimmed = trim((string) $value, " \t");
            $this->assertValue($trimmed);
            return $trimmed;
        }, array_values($values));
    }
    private function assertHeader($header): void
    {
        if (!is_string($header)) {
            throw new InvalidArgumentException(sprintf('Header name must be a string but %s provided.', is_object($header) ? get_class($header) : gettype($header)));
        }
        if (!preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/D', $header)) {
            throw new InvalidArgumentException(sprintf('"%s" is not valid header name.', $header));
        }
    }
    private function assertValue(string $value): void
    {
        if (!preg_match('/^[\x20\x09\x21-\x7E\x80-\xFF]*$/D', $value)) {
            throw new InvalidArgumentException(sprintf('"%s" is not valid header value.', $value));
        }
    }
}
