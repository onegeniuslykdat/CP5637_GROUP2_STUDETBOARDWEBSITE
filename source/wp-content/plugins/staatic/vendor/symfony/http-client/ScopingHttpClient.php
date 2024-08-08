<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
class ScopingHttpClient implements HttpClientInterface, ResetInterface, LoggerAwareInterface
{
    use HttpClientTrait;
    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var mixed[]
     */
    private $defaultOptionsByRegexp;
    /**
     * @var string|null
     */
    private $defaultRegexp;
    public function __construct(HttpClientInterface $client, array $defaultOptionsByRegexp, string $defaultRegexp = null)
    {
        $this->client = $client;
        $this->defaultOptionsByRegexp = $defaultOptionsByRegexp;
        $this->defaultRegexp = $defaultRegexp;
        if (null !== $defaultRegexp && !isset($defaultOptionsByRegexp[$defaultRegexp])) {
            throw new InvalidArgumentException(sprintf('No options are mapped to the provided "%s" default regexp.', $defaultRegexp));
        }
    }
    /**
     * @param HttpClientInterface $client
     * @param string $baseUri
     * @param mixed[] $defaultOptions
     * @param string|null $regexp
     */
    public static function forBaseUri($client, $baseUri, $defaultOptions = [], $regexp = null): self
    {
        $regexp = $regexp ?? preg_quote(implode('', self::resolveUrl(self::parseUrl('.'), self::parseUrl($baseUri))));
        $defaultOptions['base_uri'] = $baseUri;
        return new self($client, [$regexp => $defaultOptions], $regexp);
    }
    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    public function request($method, $url, $options = []): ResponseInterface
    {
        $e = null;
        $url = self::parseUrl($url, $options['query'] ?? []);
        if (\is_string($options['base_uri'] ?? null)) {
            $options['base_uri'] = self::parseUrl($options['base_uri']);
        }
        try {
            $url = implode('', self::resolveUrl($url, $options['base_uri'] ?? null));
        } catch (InvalidArgumentException $e) {
            if (null === $this->defaultRegexp) {
                throw $e;
            }
            $defaultOptions = $this->defaultOptionsByRegexp[$this->defaultRegexp];
            $options = self::mergeDefaultOptions($options, $defaultOptions, \true);
            if (\is_string($options['base_uri'] ?? null)) {
                $options['base_uri'] = self::parseUrl($options['base_uri']);
            }
            $url = implode('', self::resolveUrl($url, $options['base_uri'] ?? null, $defaultOptions['query'] ?? []));
        }
        foreach ($this->defaultOptionsByRegexp as $regexp => $defaultOptions) {
            if (preg_match("{{$regexp}}A", $url)) {
                if (null === $e || $regexp !== $this->defaultRegexp) {
                    $options = self::mergeDefaultOptions($options, $defaultOptions, \true);
                }
                break;
            }
        }
        return $this->client->request($method, $url, $options);
    }
    /**
     * @param ResponseInterface|iterable $responses
     * @param float|null $timeout
     */
    public function stream($responses, $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }
    public function reset()
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }
    }
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger): void
    {
        if ($this->client instanceof LoggerAwareInterface) {
            $this->client->setLogger($logger);
        }
    }
    /**
     * @param mixed[] $options
     * @return static
     */
    public function withOptions($options)
    {
        $clone = clone $this;
        $clone->client = $this->client->withOptions($options);
        return $clone;
    }
}
