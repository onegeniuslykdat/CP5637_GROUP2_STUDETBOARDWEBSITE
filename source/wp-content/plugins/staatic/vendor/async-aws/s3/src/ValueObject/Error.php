<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

final class Error
{
    private $key;
    private $versionId;
    private $code;
    private $message;
    public function __construct(array $input)
    {
        $this->key = $input['Key'] ?? null;
        $this->versionId = $input['VersionId'] ?? null;
        $this->code = $input['Code'] ?? null;
        $this->message = $input['Message'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getCode(): ?string
    {
        return $this->code;
    }
    public function getKey(): ?string
    {
        return $this->key;
    }
    public function getMessage(): ?string
    {
        return $this->message;
    }
    public function getVersionId(): ?string
    {
        return $this->versionId;
    }
}
