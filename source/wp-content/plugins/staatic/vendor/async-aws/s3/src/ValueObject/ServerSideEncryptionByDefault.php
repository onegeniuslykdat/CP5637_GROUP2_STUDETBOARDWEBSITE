<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\S3\Enum\ServerSideEncryption;
final class ServerSideEncryptionByDefault
{
    private $sseAlgorithm;
    private $kmsMasterKeyId;
    public function __construct(array $input)
    {
        $this->sseAlgorithm = $input['SSEAlgorithm'] ?? $this->throwException(new InvalidArgument('Missing required field "SSEAlgorithm".'));
        $this->kmsMasterKeyId = $input['KMSMasterKeyID'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getKmsMasterKeyId(): ?string
    {
        return $this->kmsMasterKeyId;
    }
    public function getSseAlgorithm(): string
    {
        return $this->sseAlgorithm;
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
