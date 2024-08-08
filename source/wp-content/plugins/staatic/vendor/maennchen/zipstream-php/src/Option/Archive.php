<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream\Option;

use Staatic\Vendor\Psr\Http\Message\StreamInterface;
final class Archive
{
    public const DEFAULT_DEFLATE_LEVEL = 6;
    private $comment = '';
    private $largeFileSize = 20 * 1024 * 1024;
    private $largeFileMethod;
    private $sendHttpHeaders = \false;
    private $httpHeaderCallback = 'header';
    private $enableZip64 = \true;
    private $zeroHeader = \false;
    private $statFiles = \true;
    private $flushOutput = \false;
    private $contentDisposition = 'attachment';
    private $contentType = 'application/x-zip';
    private $deflateLevel = 6;
    private $outputStream;
    public function __construct()
    {
        $this->largeFileMethod = Method::STORE();
        $this->outputStream = fopen('php://output', 'wb');
    }
    public function getComment(): string
    {
        return $this->comment;
    }
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }
    public function getLargeFileSize(): int
    {
        return $this->largeFileSize;
    }
    public function setLargeFileSize(int $largeFileSize): void
    {
        $this->largeFileSize = $largeFileSize;
    }
    public function getLargeFileMethod(): Method
    {
        return $this->largeFileMethod;
    }
    public function setLargeFileMethod(Method $largeFileMethod): void
    {
        $this->largeFileMethod = $largeFileMethod;
    }
    public function isSendHttpHeaders(): bool
    {
        return $this->sendHttpHeaders;
    }
    public function setSendHttpHeaders(bool $sendHttpHeaders): void
    {
        $this->sendHttpHeaders = $sendHttpHeaders;
    }
    public function getHttpHeaderCallback(): callable
    {
        return $this->httpHeaderCallback;
    }
    public function setHttpHeaderCallback(callable $httpHeaderCallback): void
    {
        $this->httpHeaderCallback = $httpHeaderCallback;
    }
    public function isEnableZip64(): bool
    {
        return $this->enableZip64;
    }
    public function setEnableZip64(bool $enableZip64): void
    {
        $this->enableZip64 = $enableZip64;
    }
    public function isZeroHeader(): bool
    {
        return $this->zeroHeader;
    }
    public function setZeroHeader(bool $zeroHeader): void
    {
        $this->zeroHeader = $zeroHeader;
    }
    public function isFlushOutput(): bool
    {
        return $this->flushOutput;
    }
    public function setFlushOutput(bool $flushOutput): void
    {
        $this->flushOutput = $flushOutput;
    }
    public function isStatFiles(): bool
    {
        return $this->statFiles;
    }
    public function setStatFiles(bool $statFiles): void
    {
        $this->statFiles = $statFiles;
    }
    public function getContentDisposition(): string
    {
        return $this->contentDisposition;
    }
    public function setContentDisposition(string $contentDisposition): void
    {
        $this->contentDisposition = $contentDisposition;
    }
    public function getContentType(): string
    {
        return $this->contentType;
    }
    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }
    public function getOutputStream()
    {
        return $this->outputStream;
    }
    public function setOutputStream($outputStream): void
    {
        $this->outputStream = $outputStream;
    }
    public function getDeflateLevel(): int
    {
        return $this->deflateLevel;
    }
    public function setDeflateLevel(int $deflateLevel): void
    {
        $this->deflateLevel = $deflateLevel;
    }
}
