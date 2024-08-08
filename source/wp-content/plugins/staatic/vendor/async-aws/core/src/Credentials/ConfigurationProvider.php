<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Credentials;

use Exception;
use Staatic\Vendor\AsyncAws\Core\Configuration;
use Staatic\Vendor\AsyncAws\Core\Exception\RuntimeException;
use Staatic\Vendor\AsyncAws\Core\Sts\StsClient;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
final class ConfigurationProvider implements CredentialProvider
{
    use DateFromResult;
    private $logger;
    private $httpClient;
    public function __construct(?HttpClientInterface $httpClient = null, ?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->httpClient = $httpClient;
    }
    /**
     * @param Configuration $configuration
     */
    public function getCredentials($configuration): ?Credentials
    {
        $accessKeyId = $configuration->get(Configuration::OPTION_ACCESS_KEY_ID);
        $secretAccessKeyId = $configuration->get(Configuration::OPTION_SECRET_ACCESS_KEY);
        if (null === $accessKeyId || null === $secretAccessKeyId) {
            return null;
        }
        $credentials = new Credentials($accessKeyId, $secretAccessKeyId, $configuration->get(Configuration::OPTION_SESSION_TOKEN));
        $roleArn = $configuration->get(Configuration::OPTION_ROLE_ARN);
        if (null !== $roleArn) {
            $region = $configuration->get(Configuration::OPTION_REGION);
            $roleSessionName = $configuration->get(Configuration::OPTION_ROLE_SESSION_NAME);
            return $this->getCredentialsFromRole($credentials, $region, $roleArn, $roleSessionName);
        }
        return $credentials;
    }
    private function getCredentialsFromRole(Credentials $credentials, string $region, string $roleArn, ?string $roleSessionName = null): ?Credentials
    {
        $roleSessionName = $roleSessionName ?? uniqid('async-aws-', \true);
        $stsClient = new StsClient(['region' => $region], $credentials, $this->httpClient);
        $result = $stsClient->assumeRole(['RoleArn' => $roleArn, 'RoleSessionName' => $roleSessionName]);
        try {
            if (null === $credentials = $result->getCredentials()) {
                throw new RuntimeException('The AsumeRole response does not contains credentials');
            }
        } catch (Exception $e) {
            $this->logger->warning('Failed to get credentials from assumed role: {exception}".', ['exception' => $e]);
            return null;
        }
        return new Credentials($credentials->getAccessKeyId(), $credentials->getSecretAccessKey(), $credentials->getSessionToken(), Credentials::adjustExpireDate($credentials->getExpiration(), $this->getDateFromResult($result)));
    }
}
