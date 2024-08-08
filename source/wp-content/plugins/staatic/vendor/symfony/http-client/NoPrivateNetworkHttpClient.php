<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use LogicException;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
use Staatic\Vendor\Symfony\Component\HttpFoundation\IpUtils;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
final class NoPrivateNetworkHttpClient implements HttpClientInterface, LoggerAwareInterface, ResetInterface
{
    use HttpClientTrait;
    private const PRIVATE_SUBNETS = ['127.0.0.0/8', '10.0.0.0/8', '192.168.0.0/16', '172.16.0.0/12', '169.254.0.0/16', '0.0.0.0/8', '240.0.0.0/4', '::1/128', 'fc00::/7', 'fe80::/10', '::ffff:0:0/96', '::/128'];
    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var mixed[]|string|null
     */
    private $subnets;
    /**
     * @param string|mixed[] $subnets
     */
    public function __construct(HttpClientInterface $client, $subnets = null)
    {
        if (!class_exists(IpUtils::class)) {
            throw new LogicException(sprintf('You cannot use "%s" if the HttpFoundation component is not installed. Try running "composer require symfony/http-foundation".', __CLASS__));
        }
        $this->client = $client;
        $this->subnets = $subnets;
    }
    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    public function request($method, $url, $options = []): ResponseInterface
    {
        $onProgress = $options['on_progress'] ?? null;
        if (null !== $onProgress && !\is_callable($onProgress)) {
            throw new InvalidArgumentException(sprintf('Option "on_progress" must be callable, "%s" given.', get_debug_type($onProgress)));
        }
        $subnets = $this->subnets;
        $lastPrimaryIp = '';
        $options['on_progress'] = function (int $dlNow, int $dlSize, array $info) use ($onProgress, $subnets, &$lastPrimaryIp): void {
            if ($info['primary_ip'] !== $lastPrimaryIp) {
                if ($info['primary_ip'] && IpUtils::checkIp($info['primary_ip'], $subnets ?? self::PRIVATE_SUBNETS)) {
                    throw new TransportException(sprintf('IP "%s" is blocked for "%s".', $info['primary_ip'], $info['url']));
                }
                $lastPrimaryIp = $info['primary_ip'];
            }
            null !== $onProgress && $onProgress($dlNow, $dlSize, $info);
        };
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
    public function reset()
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }
    }
}
