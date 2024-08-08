<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\ValueObject;

final class PolicyDescriptorType
{
    private $arn;
    public function __construct(array $input)
    {
        $this->arn = $input['arn'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getArn(): ?string
    {
        return $this->arn;
    }
    public function requestBody(): array
    {
        $payload = [];
        if (null !== $v = $this->arn) {
            $payload['arn'] = $v;
        }
        return $payload;
    }
}
