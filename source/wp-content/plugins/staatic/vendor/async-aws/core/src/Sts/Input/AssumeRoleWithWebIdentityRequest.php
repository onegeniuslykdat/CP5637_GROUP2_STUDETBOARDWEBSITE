<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\Input;

use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\Core\Sts\ValueObject\PolicyDescriptorType;
final class AssumeRoleWithWebIdentityRequest extends Input
{
    private $roleArn;
    private $roleSessionName;
    private $webIdentityToken;
    private $providerId;
    private $policyArns;
    private $policy;
    private $durationSeconds;
    public function __construct(array $input = [])
    {
        $this->roleArn = $input['RoleArn'] ?? null;
        $this->roleSessionName = $input['RoleSessionName'] ?? null;
        $this->webIdentityToken = $input['WebIdentityToken'] ?? null;
        $this->providerId = $input['ProviderId'] ?? null;
        $this->policyArns = isset($input['PolicyArns']) ? array_map([PolicyDescriptorType::class, 'create'], $input['PolicyArns']) : null;
        $this->policy = $input['Policy'] ?? null;
        $this->durationSeconds = $input['DurationSeconds'] ?? null;
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
    public function getPolicy(): ?string
    {
        return $this->policy;
    }
    public function getPolicyArns(): array
    {
        return $this->policyArns ?? [];
    }
    public function getProviderId(): ?string
    {
        return $this->providerId;
    }
    public function getRoleArn(): ?string
    {
        return $this->roleArn;
    }
    public function getRoleSessionName(): ?string
    {
        return $this->roleSessionName;
    }
    public function getWebIdentityToken(): ?string
    {
        return $this->webIdentityToken;
    }
    public function request(): Request
    {
        $headers = ['content-type' => 'application/x-www-form-urlencoded'];
        $query = [];
        $uriString = '/';
        $body = http_build_query(['Action' => 'AssumeRoleWithWebIdentity', 'Version' => '2011-06-15'] + $this->requestBody(), '', '&', \PHP_QUERY_RFC1738);
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
     * @param string|null $value
     */
    public function setProviderId($value): self
    {
        $this->providerId = $value;
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
    public function setWebIdentityToken($value): self
    {
        $this->webIdentityToken = $value;
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
        if (null === $v = $this->webIdentityToken) {
            throw new InvalidArgument(sprintf('Missing parameter "WebIdentityToken" for "%s". The value cannot be null.', __CLASS__));
        }
        $payload['WebIdentityToken'] = $v;
        if (null !== $v = $this->providerId) {
            $payload['ProviderId'] = $v;
        }
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
        return $payload;
    }
}
