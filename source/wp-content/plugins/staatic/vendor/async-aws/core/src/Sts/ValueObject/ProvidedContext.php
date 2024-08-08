<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\ValueObject;

final class ProvidedContext
{
    private $providerArn;
    private $contextAssertion;
    public function __construct(array $input)
    {
        $this->providerArn = $input['ProviderArn'] ?? null;
        $this->contextAssertion = $input['ContextAssertion'] ?? null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getContextAssertion(): ?string
    {
        return $this->contextAssertion;
    }
    public function getProviderArn(): ?string
    {
        return $this->providerArn;
    }
    public function requestBody(): array
    {
        $payload = [];
        if (null !== $v = $this->providerArn) {
            $payload['ProviderArn'] = $v;
        }
        if (null !== $v = $this->contextAssertion) {
            $payload['ContextAssertion'] = $v;
        }
        return $payload;
    }
}
