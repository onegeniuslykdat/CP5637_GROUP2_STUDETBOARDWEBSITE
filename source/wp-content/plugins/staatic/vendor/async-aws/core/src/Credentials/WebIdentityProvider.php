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
final class WebIdentityProvider implements CredentialProvider
{
    use DateFromResult;
    private $iniFileLoader;
    private $logger;
    private $httpClient;
    public function __construct(?LoggerInterface $logger = null, ?IniFileLoader $iniFileLoader = null, ?HttpClientInterface $httpClient = null)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->iniFileLoader = $iniFileLoader ?? new IniFileLoader($this->logger);
        $this->httpClient = $httpClient;
    }
    /**
     * @param Configuration $configuration
     */
    public function getCredentials($configuration): ?Credentials
    {
        $roleArn = $configuration->get(Configuration::OPTION_ROLE_ARN);
        $tokenFile = $configuration->get(Configuration::OPTION_WEB_IDENTITY_TOKEN_FILE);
        if ($tokenFile && $roleArn) {
            return $this->getCredentialsFromRole($roleArn, $tokenFile, $configuration->get(Configuration::OPTION_ROLE_SESSION_NAME), $configuration->get(Configuration::OPTION_REGION));
        }
        $profilesData = $this->iniFileLoader->loadProfiles([$configuration->get(Configuration::OPTION_SHARED_CREDENTIALS_FILE), $configuration->get(Configuration::OPTION_SHARED_CONFIG_FILE)]);
        if (empty($profilesData)) {
            return null;
        }
        $profile = $configuration->get(Configuration::OPTION_PROFILE);
        if (!isset($profilesData[$profile])) {
            $this->logger->warning('Profile "{profile}" not found.', ['profile' => $profile]);
            return null;
        }
        $profileData = $profilesData[$profile];
        $roleArn = $profileData[IniFileLoader::KEY_ROLE_ARN] ?? null;
        $tokenFile = $profileData[IniFileLoader::KEY_WEB_IDENTITY_TOKEN_FILE] ?? null;
        if (null !== $roleArn && null !== $tokenFile) {
            return $this->getCredentialsFromRole($roleArn, $tokenFile, $profileData[IniFileLoader::KEY_ROLE_SESSION_NAME] ?? null, $profileData[IniFileLoader::KEY_REGION] ?? $configuration->get(Configuration::OPTION_REGION));
        }
        return null;
    }
    private function getCredentialsFromRole(string $roleArn, string $tokenFile, ?string $sessionName, ?string $region): ?Credentials
    {
        $sessionName = $sessionName ?? uniqid('async-aws-', \true);
        if (!preg_match("/^\\w\\:|^\\/|^\\\\/", $tokenFile)) {
            $this->logger->warning('WebIdentityTokenFile "{tokenFile}" must be an absolute path.', ['tokenFile' => $tokenFile]);
        }
        try {
            $token = $this->getTokenFileContent($tokenFile);
        } catch (Exception $e) {
            $this->logger->warning('"Error reading WebIdentityTokenFile "{tokenFile}.', ['tokenFile' => $tokenFile, 'exception' => $e]);
            return null;
        }
        $stsClient = new StsClient(['region' => $region], new NullProvider(), $this->httpClient);
        $result = $stsClient->assumeRoleWithWebIdentity(['RoleArn' => $roleArn, 'RoleSessionName' => $sessionName, 'WebIdentityToken' => $token]);
        try {
            if (null === $credentials = $result->getCredentials()) {
                throw new RuntimeException('The AssumeRoleWithWebIdentity response does not contains credentials');
            }
        } catch (Exception $e) {
            $this->logger->warning('Failed to get credentials from assumed role: {exception}".', ['exception' => $e]);
            return null;
        }
        return new Credentials($credentials->getAccessKeyId(), $credentials->getSecretAccessKey(), $credentials->getSessionToken(), Credentials::adjustExpireDate($credentials->getExpiration(), $this->getDateFromResult($result)));
    }
    private function getTokenFileContent(string $tokenFile): string
    {
        $token = @file_get_contents($tokenFile);
        if (\false !== $token) {
            return $token;
        }
        $tokenDir = \dirname($tokenFile);
        $tokenLink = readlink($tokenFile);
        clearstatcache(\true, $tokenDir . \DIRECTORY_SEPARATOR . $tokenLink);
        clearstatcache(\true, $tokenDir . \DIRECTORY_SEPARATOR . \dirname($tokenLink));
        clearstatcache(\true, $tokenFile);
        if (\false === $token = file_get_contents($tokenFile)) {
            throw new RuntimeException('Failed to read data');
        }
        return $token;
    }
}
