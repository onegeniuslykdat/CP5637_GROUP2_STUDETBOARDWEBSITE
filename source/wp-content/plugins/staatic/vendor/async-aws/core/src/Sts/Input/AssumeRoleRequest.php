<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\Input;

use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\Core\Sts\ValueObject\PolicyDescriptorType;
use Staatic\Vendor\AsyncAws\Core\Sts\ValueObject\ProvidedContext;
use Staatic\Vendor\AsyncAws\Core\Sts\ValueObject\Tag;
final class AssumeRoleRequest extends Input
{
    private $roleArn;
    private $roleSessionName;
    private $policyArns;
    private $policy;
    private $durationSeconds;
    private $tags;
    private $transitiveTagKeys;
    private $externalId;
    private $serialNumber;
    private $tokenCode;
    private $sourceIdentity;
    private $providedContexts;
    public function __construct(array $input = [])
    {
        $this->roleArn = $input['RoleArn'] ?? null;
        $this->roleSessionName = $input['RoleSessionName'] ?? null;
        $this->policyArns = isset($input['PolicyArns']) ? array_map([PolicyDescriptorType::class, 'create'], $input['PolicyArns']) : null;
        $this->policy = $input['Policy'] ?? null;
        $this->durationSeconds = $input['DurationSeconds'] ?? null;
        $this->tags = isset($input['Tags']) ? array_map([Tag::class, 'create'], $input['Tags']) : null;
        $this->transitiveTagKeys = $input['TransitiveTagKeys'] ?? null;
        $this->externalId = $input['ExternalId'] ?? null;
        $this->serialNumber = $input['SerialNumber'] ?? null;
        $this->tokenCode = $input['TokenCode'] ?? null;
        $this->sourceIdentity = $input['SourceIdentity'] ?? null;
        $this->providedContexts = isset($input['ProvidedContexts']) ? array_map([ProvidedContext::class, 'create'], $input['ProvidedContexts']) : null;
        parent::__construct($input);
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getDurationSeconds(): ?int
    {
        return $this->durationSeconds;
    }
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
    public function getPolicy(): ?string
    {
        return $this->policy;
    }
    public function getPolicyArns(): array
    {
        return $this->policyArns ?? [];
    }
    public function getProvidedContexts(): array
    {
        return $this->providedContexts ?? [];
    }
    public function getRoleArn(): ?string
    {
        return $this->roleArn;
    }
    public function getRoleSessionName(): ?string
    {
        return $this->roleSessionName;
    }
    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }
    public function getSourceIdentity(): ?string
    {
        return $this->sourceIdentity;
    }
    public function getTags(): array
    {
        return $this->tags ?? [];
    }
    public function getTokenCode(): ?string
    {
        return $this->tokenCode;
    }
    public function getTransitiveTagKeys(): array
    {
        return $this->transitiveTagKeys ?? [];
    }
    public function request(): Request
    {
        $headers = ['content-type' => 'application/x-www-form-urlencoded'];
        $query = [];
        $uriString = '/';
        $body = http_build_query(['Action' => 'AssumeRole', 'Version' => '2011-06-15'] + $this->requestBody(), '', '&', \PHP_QUERY_RFC1738);
        return new Request('POST', $uriString, $query, $headers, StreamFactory::create($body));
    }
    /**
     * @param int|null $value
     */
    public function setDurationSeconds($value): self
    {
        $this->durationSeconds = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setExternalId($value): self
    {
        $this->externalId = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setPolicy($value): self
    {
        $this->policy = $value;
        return $this;
    }
    /**
     * @param mixed[] $value
     */
    public function setPolicyArns($value): self
    {
        $this->policyArns = $value;
        return $this;
    }
    /**
     * @param mixed[] $value
     */
    public function setProvidedContexts($value): self
    {
        $this->providedContexts = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setRoleArn($value): self
    {
        $this->roleArn = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setRoleSessionName($value): self
    {
        $this->roleSessionName = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setSerialNumber($value): self
    {
        $this->serialNumber = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setSourceIdentity($value): self
    {
        $this->sourceIdentity = $value;
        return $this;
    }
    /**
     * @param mixed[] $value
     */
    public function setTags($value): self
    {
        $this->tags = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setTokenCode($value): self
    {
        $this->tokenCode = $value;
        return $this;
    }
    /**
     * @param mixed[] $value
     */
    public function setTransitiveTagKeys($value): self
    {
        $this->transitiveTagKeys = $value;
        return $this;
    }
    private function requestBody(): array
    {
        $payload = [];
        if (null === $v = $this->roleArn) {
            throw new InvalidArgument(sprintf('Missing parameter "RoleArn" for "%s". The value cannot be null.', __CLASS__));
        }
        $payload['RoleArn'] = $v;
        if (null === $v = $this->roleSessionName) {
            throw new InvalidArgument(sprintf('Missing parameter "RoleSessionName" for "%s". The value cannot be null.', __CLASS__));
        }
        $payload['RoleSessionName'] = $v;
        if (null !== $v = $this->policyArns) {
            $index = 0;
            foreach ($v as $mapValue) {
                ++$index;
                foreach ($mapValue->requestBody() as $bodyKey => $bodyValue) {
                    $payload["PolicyArns.member.{$index}.{$bodyKey}"] = $bodyValue;
                }
            }
        }
        if (null !== $v = $this->policy) {
            $payload['Policy'] = $v;
        }
        if (null !== $v = $this->durationSeconds) {
            $payload['DurationSeconds'] = $v;
        }
        if (null !== $v = $this->tags) {
            $index = 0;
            foreach ($v as $mapValue) {
                ++$index;
                foreach ($mapValue->requestBody() as $bodyKey => $bodyValue) {
                    $payload["Tags.member.{$index}.{$bodyKey}"] = $bodyValue;
                }
            }
        }
        if (null !== $v = $this->transitiveTagKeys) {
            $index = 0;
            foreach ($v as $mapValue) {
                ++$index;
                $payload["TransitiveTagKeys.member.{$index}"] = $mapValue;
            }
        }
        if (null !== $v = $this->externalId) {
            $payload['ExternalId'] = $v;
        }
        if (null !== $v = $this->serialNumber) {
            $payload['SerialNumber'] = $v;
        }
        if (null !== $v = $this->tokenCode) {
            $payload['TokenCode'] = $v;
        }
        if (null !== $v = $this->sourceIdentity) {
            $payload['SourceIdentity'] = $v;
        }
        if (null !== $v = $this->providedContexts) {
            $index = 0;
            foreach ($v as $mapValue) {
                ++$index;
                foreach ($mapValue->requestBody() as $bodyKey => $bodyValue) {
                    $payload["ProvidedContexts.member.{$index}.{$bodyKey}"] = $bodyValue;
                }
            }
        }
        return $payload;
    }
}
