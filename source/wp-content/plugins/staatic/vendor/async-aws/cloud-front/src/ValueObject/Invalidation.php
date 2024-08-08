<?php

namespace Staatic\Vendor\AsyncAws\CloudFront\ValueObject;

use DateTimeImmutable;
use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class Invalidation
{
    private $id;
    private $status;
    private $createTime;
    private $invalidationBatch;
    public function __construct(array $input)
    {
        $this->id = $input['Id'] ?? $this->throwException(new InvalidArgument('Missing required field "Id".'));
        $this->status = $input['Status'] ?? $this->throwException(new InvalidArgument('Missing required field "Status".'));
        $this->createTime = $input['CreateTime'] ?? $this->throwException(new InvalidArgument('Missing required field "CreateTime".'));
        $this->invalidationBatch = isset($input['InvalidationBatch']) ? InvalidationBatch::create($input['InvalidationBatch']) : $this->throwException(new InvalidArgument('Missing required field "InvalidationBatch".'));
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getCreateTime(): DateTimeImmutable
    {
        return $this->createTime;
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getInvalidationBatch(): InvalidationBatch
    {
        return $this->invalidationBatch;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
