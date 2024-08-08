<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream\Option;

use DateTime;
use DateTimeInterface;
final class File
{
    private $comment = '';
    private $method;
    private $deflateLevel;
    private $time;
    private $size = 0;
    public function defaultTo(Archive $archiveOptions): void
    {
        $this->deflateLevel = $this->deflateLevel ?: $archiveOptions->getDeflateLevel();
        $this->time = $this->time ?: new DateTime();
    }
    public function getComment(): string
    {
        return $this->comment;
    }
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }
    public function getMethod(): Method
    {
        return $this->method ?: Method::DEFLATE();
    }
    public function setMethod(Method $method): void
    {
        $this->method = $method;
    }
    public function getDeflateLevel(): int
    {
        return $this->deflateLevel ?: Archive::DEFAULT_DEFLATE_LEVEL;
    }
    public function setDeflateLevel(int $deflateLevel): void
    {
        $this->deflateLevel = $deflateLevel;
    }
    public function getTime(): DateTimeInterface
    {
        return $this->time;
    }
    public function setTime(DateTimeInterface $time): void
    {
        $this->time = $time;
    }
    public function getSize(): int
    {
        return $this->size;
    }
    public function setSize(int $size): void
    {
        $this->size = $size;
    }
}
