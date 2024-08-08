<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Credentials;

use DateTimeImmutable;
use DateInterval;
use Staatic\Vendor\AsyncAws\Core\Configuration;
final class Credentials implements CredentialProvider
{
    private const EXPIRATION_DRIFT = 30;
    private $accessKeyId;
    private $secretKey;
    private $sessionToken;
    private $expireDate;
    public function __construct(string $accessKeyId, string $secretKey, ?string $sessionToken = null, ?DateTimeImmutable $expireDate = null)
    {
        $this->accessKeyId = $accessKeyId;
        $this->secretKey = $secretKey;
        $this->sessionToken = $sessionToken;
        $this->expireDate = $expireDate;
    }
    public function getAccessKeyId(): string
    {
        return $this->accessKeyId;
    }
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }
    public function getSessionToken(): ?string
    {
        return $this->sessionToken;
    }
    public function getExpireDate(): ?DateTimeImmutable
    {
        return $this->expireDate;
    }
    public function isExpired(): bool
    {
        return null !== $this->expireDate && new DateTimeImmutable() >= $this->expireDate;
    }
    /**
     * @param Configuration $configuration
     */
    public function getCredentials($configuration): ?Credentials
    {
        return $this->isExpired() ? null : $this;
    }
    /**
     * @param DateTimeImmutable $expireDate
     * @param DateTimeImmutable|null $reference
     */
    public static function adjustExpireDate($expireDate, $reference = null): DateTimeImmutable
    {
        if (null !== $reference) {
            $expireDate = (new DateTimeImmutable())->add($reference->diff($expireDate));
        }
        return $expireDate->sub(new DateInterval(sprintf('PT%dS', self::EXPIRATION_DRIFT)));
    }
}
