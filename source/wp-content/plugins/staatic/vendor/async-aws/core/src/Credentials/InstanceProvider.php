<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Credentials;

use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\Core\Configuration;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\JsonException;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
use Staatic\Vendor\Symfony\Component\HttpClient\HttpClient;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
final class InstanceProvider implements CredentialProvider
{
    private const TOKEN_ENDPOINT = 'http://169.254.169.254/latest/api/token';
    private const METADATA_ENDPOINT = 'http://169.254.169.254/latest/meta-data/iam/security-credentials';
    private $logger;
    private $httpClient;
    private $timeout;
    private $tokenTtl;
    public function __construct(?HttpClientInterface $httpClient = null, ?LoggerInterface $logger = null, float $timeout = 1.0, int $tokenTtl = 21600)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->timeout = $timeout;
        $this->tokenTtl = $tokenTtl;
    }
    /**
     * @param Configuration $configuration
     */
    public function getCredentials($configuration): ?Credentials
    {
        $token = $this->getToken();
        $headers = [];
        if (null !== $token) {
            $headers = ['X-aws-ec2-metadata-token' => $token];
        }
        try {
            $response = $this->httpClient->request('GET', self::METADATA_ENDPOINT, ['timeout' => $this->timeout, 'headers' => $headers]);
            $profile = $response->getContent();
            $response = $this->httpClient->request('GET', self::METADATA_ENDPOINT . '/' . $profile, ['timeout' => $this->timeout, 'headers' => $headers]);
            $result = $this->toArray($response);
            if ('Success' !== $result['Code']) {
                $this->logger->info('Unexpected instance profile.', ['response_code' => $result['Code']]);
                return null;
            }
        } catch (DecodingExceptionInterface $e) {
            $this->logger->info('Failed to decode Credentials.', ['exception' => $e]);
            return null;
        } catch (TransportExceptionInterface|HttpExceptionInterface $e) {
            $this->logger->info('Failed to fetch Profile from Instance Metadata.', ['exception' => $e]);
            return null;
        }
        if (null !== $date = $response->getHeaders(\false)['date'][0] ?? null) {
            $date = new DateTimeImmutable($date);
        }
        return new Credentials($result['AccessKeyId'], $result['SecretAccessKey'], $result['Token'], Credentials::adjustExpireDate(new DateTimeImmutable($result['Expiration']), $date));
    }
    private function toArray(ResponseInterface $response): array
    {
        if ('' === $content = $response->getContent(\true)) {
            throw new TransportException('Response body is empty.');
        }
        try {
            $content = json_decode($content, \true, 512, \JSON_BIGINT_AS_STRING | ((\PHP_VERSION_ID >= 70300) ? 0 : 0));
        } catch (\JsonException $e) {
            throw new JsonException(sprintf('%s for "%s".', $e->getMessage(), $response->getInfo('url')), $e->getCode());
        }
        if (\PHP_VERSION_ID < 70300 && \JSON_ERROR_NONE !== json_last_error()) {
            throw new JsonException(sprintf('%s for "%s".', json_last_error_msg(), $response->getInfo('url')), json_last_error());
        }
        if (!\is_array($content)) {
            throw new JsonException(sprintf('JSON content was expected to decode to an array, %s returned for "%s".', \gettype($content), $response->getInfo('url')));
        }
        return $content;
    }
    private function getToken(): ?string
    {
        try {
            $response = $this->httpClient->request('PUT', self::TOKEN_ENDPOINT, ['timeout' => $this->timeout, 'headers' => ['X-aws-ec2-metadata-token-ttl-seconds' => $this->tokenTtl]]);
            return $response->getContent();
        } catch (TransportExceptionInterface|HttpExceptionInterface $e) {
            $this->logger->info('Failed to fetch metadata token for IMDSv2, fallback to IMDSv1.', ['exception' => $e]);
            return null;
        }
    }
}
