<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\ValueObject;

use DateTimeImmutable;
use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class Credentials
{
    private $accessKeyId;
    private $secretAccessKey;
    private $sessionToken;
    private $expiration;
    public function __construct(array $input)
    {
        $this->accessKeyId = $input['AccessKeyId'] ?? $this->throwException(new InvalidArgument('Missing required field "AccessKeyId".'));
        $this->secretAccessKey = $input['SecretAccessKey'] ?? $this->throwException(new InvalidArgument('Missing required field "SecretAccessKey".'));
        $this->sessionToken = $input['SessionToken'] ?? $this->throwException(new InvalidArgument('Missing required field "SessionToken".'));
        $this->expiration = $input['Expiration'] ?? $this->throwException(new InvalidArgument('Missing required field "Expiration".'));
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getAccessKeyId(): string
    {
        return $this->accessKeyId;
    }
    public function getExpiration(): DateTimeImmutable
    {
        return $this->expiration;
    }
    public function getSecretAccessKey(): string
    {
        return $this->secretAccessKey;
    }
    public function getSessionToken(): string
    {
        return $this->sessionToken;
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
