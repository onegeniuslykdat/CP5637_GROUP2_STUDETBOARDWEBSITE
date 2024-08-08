<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\ValueObject;

use Throwable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class AssumedRoleUser
{
    private $assumedRoleId;
    private $arn;
    public function __construct(array $input)
    {
        $this->assumedRoleId = $input['AssumedRoleId'] ?? $this->throwException(new InvalidArgument('Missing required field "AssumedRoleId".'));
        $this->arn = $input['Arn'] ?? $this->throwException(new InvalidArgument('Missing required field "Arn".'));
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getArn(): string
    {
        return $this->arn;
    }
    public function getAssumedRoleId(): string
    {
        return $this->assumedRoleId;
    }
    private function throwException(Throwable $exception)
    {
        throw $exception;
    }
}
