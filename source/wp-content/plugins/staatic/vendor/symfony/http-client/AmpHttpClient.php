<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use LogicException;
use Closure;
use Staatic\Vendor\Amp\CancelledException;
use Staatic\Vendor\Amp\Http\Client\DelegateHttpClient;
use Staatic\Vendor\Amp\Http\Client\InterceptedHttpClient;
use Staatic\Vendor\Amp\Http\Client\PooledHttpClient;
use Staatic\Vendor\Amp\Http\Client\Request;
use Staatic\Vendor\Amp\Http\Tunnel\Http1TunnelConnector;
use Staatic\Vendor\Amp\Promise;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\AmpClientState;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\AmpResponse;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\ResponseStream;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
if (!interface_exists(DelegateHttpClient::class)) {
    throw new LogicException('You cannot use "Symfony\Component\HttpClient\AmpHttpClient" as the "amphp/http-client" package is not installed. Try running "composer require amphp/http-client:^4.2.1".');
}
if (!interface_exists(Promise::class)) {
    throw new LogicException('You cannot use "Symfony\Component\HttpClient\AmpHttpClient" as the installed "amphp/http-client" is not compatible with this version of "symfony/http-client". Try downgrading "amphp/http-client" to "^4.2.1".');
}
final class AmpHttpClient implements HttpClientInterface, LoggerAwareInterface, ResetInterface
{
    use HttpClientTrait;
    use LoggerAwareTrait;
    /**
     * @var mixed[]
     */
    private $defaultOptions = self::OPTIONS_DEFAULTS;
    /**
     * @var mixed[]
     */
    private static $emptyDefaults = self::OPTIONS_DEFAULTS;
    /**
     * @var AmpClientState
     */
    private $multi;
    public function __construct(array $defaultOptions = [], callable $clientConfigurator = null, int $maxHostConnections = 6, int $maxPendingPushes = 50)
    {
        $this->defaultOptions['buffer'] = $this->defaultOptions['buffer'] ?? Closure::fromCallable([self::class, 'shouldBuffer']);
        if ($defaultOptions) {
            [, $this->defaultOptions] = self::prepareRequest(null, null, $defaultOptions, $this->defaultOptions);
        }
        $this->multi = new AmpClientState($clientConfigurator, $maxHostConnections, $maxPendingPushes, $this->logger);
    }
    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    public function request($method, $url, $options = []): ResponseInterface
    {
        [$url, $options] = self::prepareRequest($method, $url, $options, $this->defaultOptions);
        $options['proxy'] = self::getProxy($options['proxy'], $url, $options['no_proxy']);
        if (null !== $options['proxy'] && !class_exists(Http1TunnelConnector::class)) {
            throw new LogicException('You cannot use the "proxy" option as the "amphp/http-tunnel" package is not installed. Try running "composer require amphp/http-tunnel".');
        }
        if ($options['bindto']) {
            if (strncmp($options['bindto'], 'if!', strlen('if!')) === 0) {
                throw new TransportException(__CLASS__ . ' cannot bind to network interfaces, use e.g. CurlHttpClient instead.');
            }
            if (strncmp($options['bindto'], 'host!', strlen('host!')) === 0) {
                $options['bindto'] = substr($options['bindto'], 5);
            }
        }
        if (('' !== $options['body'] || 'POST' === $method || isset($options['normalized_headers']['content-length'])) && !isset($options['normalized_headers']['content-type'])) {
            $options['headers'][] = 'Content-Type: application/x-www-form-urlencoded';
        }
        if (!isset($options['normalized_headers']['user-agent'])) {
            $options['headers'][] = 'User-Agent: Symfony HttpClient/Amp';
        }
        if (0 < $options['max_duration']) {
            $options['timeout'] = min($options['max_duration'], $options['timeout']);
        }
        if ($options['resolve']) {
            $this->multi->dnsCache = $options['resolve'] + $this->multi->dnsCache;
        }
        if ($options['peer_fingerprint'] && !isset($options['peer_fingerprint']['pin-sha256'])) {
            throw new TransportException(__CLASS__ . ' supports only "pin-sha256" fingerprints.');
        }
        $request = new Request(implode('', $url), $method);
        if ($options['http_version']) {
            $request->setProtocolVersions((function () use ($options, $request) {
                switch ((float) $options['http_version']) {
                    case 1.0:
                        return ['1.0'];
                    case 1.1:
                        return $request->setProtocolVersions(['1.1', '1.0']);
                    default:
                        return ['2', '1.1', '1.0'];
                }
            })());
        }
        foreach ($options['headers'] as $v) {
            $h = explode(': ', $v, 2);
            $request->addHeader($h[0], $h[1]);
        }
        $request->setTcpConnectTimeout(1000 * $options['timeout']);
        $request->setTlsHandshakeTimeout(1000 * $options['timeout']);
        $request->setTransferTimeout(1000 * $options['max_duration']);
        if (method_exists($request, 'setInactivityTimeout')) {
            $request->setInactivityTimeout(0);
        }
        if ('' !== $request->getUri()->getUserInfo() && !$request->hasHeader('authorization')) {
            $auth = explode(':', $request->getUri()->getUserInfo(), 2);
            $auth = array_map('rawurldecode', $auth) + [1 => ''];
            $request->setHeader('Authorization', 'Basic ' . base64_encode(implode(':', $auth)));
        }
        return new AmpResponse($this->multi, $request, $options, $this->logger);
    }
    /**
     * @param ResponseInterface|iterable $responses
     * @param float|null $timeout
     */
    public function stream($responses, $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof AmpResponse) {
            $responses = [$responses];
        }
        return new ResponseStream(AmpResponse::stream($responses, $timeout));
    }
    public function reset()
    {
        $this->multi->dnsCache = [];
        foreach ($this->multi->pushedResponses as $authority => $pushedResponses) {
            foreach ($pushedResponses as [$pushedUrl, $pushDeferred]) {
                $pushDeferred->fail(new CancelledException());
                ($nullsafeVariable1 = $this->logger) ? $nullsafeVariable1->debug(sprintf('Unused pushed response: "%s"', $pushedUrl)) : null;
            }
        }
        $this->multi->pushedResponses = [];
    }
}
