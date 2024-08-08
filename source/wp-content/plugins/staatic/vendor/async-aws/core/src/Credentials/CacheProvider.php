<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Credentials;

use Staatic\Vendor\AsyncAws\Core\Configuration;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
final class CacheProvider implements CredentialProvider, ResetInterface
{
    private $cache = [];
    private $decorated;
    public function __construct(CredentialProvider $decorated)
    {
        $this->decorated = $decorated;
    }
    /**
     * @param Configuration $configuration
     */
    public function getCredentials($configuration): ?Credentials
    {
        $key = spl_object_hash($configuration);
        if (!\array_key_exists($key, $this->cache) || null !== $this->cache[$key] && $this->cache[$key]->isExpired()) {
            $this->cache[$key] = $this->decorated->getCredentials($configuration);
        }
        return $this->cache[$key];
    }
    public function reset(): void
    {
        $this->cache = [];
    }
}
