<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Credentials;

use Staatic\Vendor\AsyncAws\Core\Configuration;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Symfony\Component\HttpClient\HttpClient;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
final class ChainProvider implements CredentialProvider, ResetInterface
{
    private $providers;
    private $lastSuccessfulProvider = [];
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }
    /**
     * @param Configuration $configuration
     */
    public function getCredentials($configuration): ?Credentials
    {
        $key = spl_object_hash($configuration);
        if (\array_key_exists($key, $this->lastSuccessfulProvider)) {
            if (null === $provider = $this->lastSuccessfulProvider[$key]) {
                return null;
            }
            return $provider->getCredentials($configuration);
        }
        foreach ($this->providers as $provider) {
            if (null !== $credentials = $provider->getCredentials($configuration)) {
                $this->lastSuccessfulProvider[$key] = $provider;
                return $credentials;
            }
        }
        $this->lastSuccessfulProvider[$key] = null;
        return null;
    }
    public function reset(): void
    {
        $this->lastSuccessfulProvider = [];
    }
    /**
     * @param HttpClientInterface|null $httpClient
     * @param LoggerInterface|null $logger
     */
    public static function createDefaultChain($httpClient = null, $logger = null): CredentialProvider
    {
        $httpClient = $httpClient ?? HttpClient::create();
        $logger = $logger ?? new NullLogger();
        return new ChainProvider([new ConfigurationProvider(), new WebIdentityProvider($logger, null, $httpClient), new IniFileProvider($logger, null, $httpClient), new ContainerProvider($httpClient, $logger), new InstanceProvider($httpClient, $logger)]);
    }
}
