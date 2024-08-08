<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Internal;

use Closure;
use Throwable;
use Staatic\Vendor\Amp\Http\Client\Connection\Stream;
use Staatic\Vendor\Amp\Http\Client\EventListener;
use Staatic\Vendor\Amp\Http\Client\Request;
use Staatic\Vendor\Amp\Promise;
use Staatic\Vendor\Amp\Success;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
class AmpListener implements EventListener
{
    /**
     * @var mixed[]
     */
    private $info;
    /**
     * @var mixed[]
     */
    private $pinSha256;
    /**
     * @var Closure
     */
    private $onProgress;
    private $handle;
    public function __construct(array &$info, array $pinSha256, Closure $onProgress, &$handle)
    {
        $info += ['connect_time' => 0.0, 'pretransfer_time' => 0.0, 'starttransfer_time' => 0.0, 'total_time' => 0.0, 'namelookup_time' => 0.0, 'primary_ip' => '', 'primary_port' => 0];
        $this->info =& $info;
        $this->pinSha256 = $pinSha256;
        $this->onProgress = $onProgress;
        $this->handle =& $handle;
    }
    /**
     * @param Request $request
     */
    public function startRequest($request): Promise
    {
        $this->info['start_time'] = $this->info['start_time'] ?? microtime(\true);
        ($this->onProgress)();
        return new Success();
    }
    /**
     * @param Request $request
     */
    public function startDnsResolution($request): Promise
    {
        ($this->onProgress)();
        return new Success();
    }
    /**
     * @param Request $request
     */
    public function startConnectionCreation($request): Promise
    {
        ($this->onProgress)();
        return new Success();
    }
    /**
     * @param Request $request
     */
    public function startTlsNegotiation($request): Promise
    {
        ($this->onProgress)();
        return new Success();
    }
    /**
     * @param Request $request
     * @param Stream $stream
     */
    public function startSendingRequest($request, $stream): Promise
    {
        $host = $stream->getRemoteAddress()->getHost();
        if (strpos($host, ':') !== false) {
            $host = '[' . $host . ']';
        }
        $this->info['primary_ip'] = $host;
        $this->info['primary_port'] = $stream->getRemoteAddress()->getPort();
        $this->info['pretransfer_time'] = microtime(\true) - $this->info['start_time'];
        $this->info['debug'] .= sprintf("* Connected to %s (%s) port %d\n", $request->getUri()->getHost(), $host, $this->info['primary_port']);
        if ((isset($this->info['peer_certificate_chain']) || $this->pinSha256) && null !== $tlsInfo = $stream->getTlsInfo()) {
            foreach ($tlsInfo->getPeerCertificates() as $cert) {
                $this->info['peer_certificate_chain'][] = openssl_x509_read($cert->toPem());
            }
            if ($this->pinSha256) {
                $pin = openssl_pkey_get_public($this->info['peer_certificate_chain'][0]);
                $pin = openssl_pkey_get_details($pin)['key'];
                $pin = \array_slice(explode("\n", $pin), 1, -2);
                $pin = base64_decode(implode('', $pin));
                $pin = base64_encode(hash('sha256', $pin, \true));
                if (!\in_array($pin, $this->pinSha256, \true)) {
                    throw new TransportException(sprintf('SSL public key does not match pinned public key for "%s".', $this->info['url']));
                }
            }
        }
        ($this->onProgress)();
        $uri = $request->getUri();
        $requestUri = $uri->getPath() ?: '/';
        if ('' !== $query = $uri->getQuery()) {
            $requestUri .= '?' . $query;
        }
        if ('CONNECT' === $method = $request->getMethod()) {
            $requestUri = $uri->getHost() . ': ' . ($uri->getPort() ?? (('https' === $uri->getScheme()) ? 443 : 80));
        }
        $this->info['debug'] .= sprintf("> %s %s HTTP/%s \r\n", $method, $requestUri, $request->getProtocolVersions()[0]);
        foreach ($request->getRawHeaders() as [$name, $value]) {
            $this->info['debug'] .= $name . ': ' . $value . "\r\n";
        }
        $this->info['debug'] .= "\r\n";
        return new Success();
    }
    /**
     * @param Request $request
     * @param Stream $stream
     */
    public function completeSendingRequest($request, $stream): Promise
    {
        ($this->onProgress)();
        return new Success();
    }
    /**
     * @param Request $request
     * @param Stream $stream
     */
    public function startReceivingResponse($request, $stream): Promise
    {
        $this->info['starttransfer_time'] = microtime(\true) - $this->info['start_time'];
        ($this->onProgress)();
        return new Success();
    }
    /**
     * @param Request $request
     * @param Stream $stream
     */
    public function completeReceivingResponse($request, $stream): Promise
    {
        $this->handle = null;
        ($this->onProgress)();
        return new Success();
    }
    /**
     * @param Request $request
     */
    public function completeDnsResolution($request): Promise
    {
        $this->info['namelookup_time'] = microtime(\true) - $this->info['start_time'];
        ($this->onProgress)();
        return new Success();
    }
    /**
     * @param Request $request
     */
    public function completeConnectionCreation($request): Promise
    {
        $this->info['connect_time'] = microtime(\true) - $this->info['start_time'];
        ($this->onProgress)();
        return new Success();
    }
    /**
     * @param Request $request
     */
    public function completeTlsNegotiation($request): Promise
    {
        ($this->onProgress)();
        return new Success();
    }
    /**
     * @param Request $request
     * @param Throwable $cause
     */
    public function abort($request, $cause): Promise
    {
        return new Success();
    }
}
