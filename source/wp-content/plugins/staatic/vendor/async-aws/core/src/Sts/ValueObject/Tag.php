<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\ValueObject;

use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class Tag
{
    private $key;
    private $value;
    public function __construct(array $input)
    {
        $this->key = $input['Key'] ?? $this->throwException(new InvalidArgument('Missing required field "Key".'));
        $this->value = $input['Value'] ?? $this->throwException(new InvalidArgument('Missing required field "Value".'));
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getKey(): string
    {
        return $this->key;
    }
    public function getValue(): string
    {
        return $this->value;
    }
    public function requestBody(): array
    {
        $payload = [];
        $v = $this->key;
        $payload['Key'] = $v;
        $v = $this->value;
        $payload['Value'] = $v;
        return $payload;
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
