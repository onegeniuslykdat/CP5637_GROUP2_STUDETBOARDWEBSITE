<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use LogicException;
use Closure;
use CurlMultiHandle;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\CurlClientState;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\PushedResponse;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\CurlResponse;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\ResponseStream;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
final class CurlHttpClient implements HttpClientInterface, LoggerAwareInterface, ResetInterface
{
    use HttpClientTrait;
    /**
     * @var mixed[]
     */
    private $defaultOptions = self::OPTIONS_DEFAULTS + ['auth_ntlm' => null, 'extra' => ['curl' => []]];
    /**
     * @var mixed[]
     */
    private static $emptyDefaults = self::OPTIONS_DEFAULTS + ['auth_ntlm' => null];
    /**
     * @var LoggerInterface|null
     */
    private $logger;
    /**
     * @var CurlClientState
     */
    private $multi;
    public function __construct(array $defaultOptions = [], int $maxHostConnections = 6, int $maxPendingPushes = 50)
    {
        if (!\extension_loaded('curl')) {
            throw new LogicException('You cannot use the "Symfony\Component\HttpClient\CurlHttpClient" as the "curl" extension is not installed.');
        }
        $this->defaultOptions['buffer'] = $this->defaultOptions['buffer'] ?? Closure::fromCallable([self::class, 'shouldBuffer']);
        if ($defaultOptions) {
            [, $this->defaultOptions] = self::prepareRequest(null, null, $defaultOptions, $this->defaultOptions);
        }
        $this->multi = new CurlClientState($maxHostConnections, $maxPendingPushes);
    }
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger): void
    {
        $this->logger = $this->multi->logger = $logger;
    }
    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    public function request($method, $url, $options = []): ResponseInterface
    {
        [$url, $options] = self::prepareRequest($method, $url, $options, $this->defaultOptions);
        $scheme = $url['scheme'];
        $authority = $url['authority'];
        $host = parse_url($authority, \PHP_URL_HOST);
        $port = parse_url($authority, \PHP_URL_PORT) ?: (('http:' === $scheme) ? 80 : 443);
        $proxy = self::getProxyUrl($options['proxy'], $url);
        $url = implode('', $url);
        if (!isset($options['normalized_headers']['user-agent'])) {
            $options['headers'][] = 'User-Agent: Symfony HttpClient/Curl';
        }
        $curlopts = [\CURLOPT_URL => $url, \CURLOPT_TCP_NODELAY => \true, \CURLOPT_PROTOCOLS => \CURLPROTO_HTTP | \CURLPROTO_HTTPS, \CURLOPT_REDIR_PROTOCOLS => \CURLPROTO_HTTP | \CURLPROTO_HTTPS, \CURLOPT_FOLLOWLOCATION => \true, \CURLOPT_MAXREDIRS => (0 < $options['max_redirects']) ? $options['max_redirects'] : 0, \CURLOPT_COOKIEFILE => '', \CURLOPT_TIMEOUT => 0, \CURLOPT_PROXY => $proxy, \CURLOPT_NOPROXY => $options['no_proxy'] ?? $_SERVER['no_proxy'] ?? $_SERVER['NO_PROXY'] ?? '', \CURLOPT_SSL_VERIFYPEER => $options['verify_peer'], \CURLOPT_SSL_VERIFYHOST => $options['verify_host'] ? 2 : 0, \CURLOPT_CAINFO => $options['cafile'], \CURLOPT_CAPATH => $options['capath'], \CURLOPT_SSL_CIPHER_LIST => $options['ciphers'], \CURLOPT_SSLCERT => $options['local_cert'], \CURLOPT_SSLKEY => $options['local_pk'], \CURLOPT_KEYPASSWD => $options['passphrase'], \CURLOPT_CERTINFO => $options['capture_peer_cert_chain']];
        if (1.0 === (float) $options['http_version']) {
            $curlopts[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_1_0;
        } elseif (1.1 === (float) $options['http_version']) {
            $curlopts[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_1_1;
        } elseif (\defined('CURL_VERSION_HTTP2') && \CURL_VERSION_HTTP2 & CurlClientState::$curlVersion['features'] && ('https:' === $scheme || 2.0 === (float) $options['http_version'])) {
            $curlopts[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_2_0;
        }
        if (isset($options['auth_ntlm'])) {
            $curlopts[\CURLOPT_HTTPAUTH] = \CURLAUTH_NTLM;
            $curlopts[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_1_1;
            if (\is_array($options['auth_ntlm'])) {
                $count = \count($options['auth_ntlm']);
                if ($count <= 0 || $count > 2) {
                    throw new InvalidArgumentException(sprintf('Option "auth_ntlm" must contain 1 or 2 elements, %d given.', $count));
                }
                $options['auth_ntlm'] = implode(':', $options['auth_ntlm']);
            }
            if (!\is_string($options['auth_ntlm'])) {
                throw new InvalidArgumentException(sprintf('Option "auth_ntlm" must be a string or an array, "%s" given.', get_debug_type($options['auth_ntlm'])));
            }
            $curlopts[\CURLOPT_USERPWD] = $options['auth_ntlm'];
        }
        if (!\ZEND_THREAD_SAFE) {
            $curlopts[\CURLOPT_DNS_USE_GLOBAL_CACHE] = \false;
        }
        if (\defined('CURLOPT_HEADEROPT') && \defined('CURLHEADER_SEPARATE')) {
            $curlopts[\CURLOPT_HEADEROPT] = \CURLHEADER_SEPARATE;
        }
        if (isset($this->multi->dnsCache->hostnames[$host])) {
            $options['resolve'] += [$host => $this->multi->dnsCache->hostnames[$host]];
        }
        if ($options['resolve'] || $this->multi->dnsCache->evictions) {
            $resolve = $this->multi->dnsCache->evictions;
            $this->multi->dnsCache->evictions = [];
            if ($resolve && 0x72a00 > CurlClientState::$curlVersion['version_number']) {
                $this->multi->reset();
            }
            foreach ($options['resolve'] as $host => $ip) {
                $resolve[] = (null === $ip) ? "-{$host}:{$port}" : "{$host}:{$port}:{$ip}";
                $this->multi->dnsCache->hostnames[$host] = $ip;
                $this->multi->dnsCache->removals["-{$host}:{$port}"] = "-{$host}:{$port}";
            }
            $curlopts[\CURLOPT_RESOLVE] = $resolve;
        }
        if ('POST' === $method) {
            $curlopts[\CURLOPT_POST] = \true;
        } elseif ('HEAD' === $method) {
            $curlopts[\CURLOPT_NOBODY] = \true;
        } else {
            $curlopts[\CURLOPT_CUSTOMREQUEST] = $method;
        }
        if ('\\' !== \DIRECTORY_SEPARATOR && $options['timeout'] < 1) {
            $curlopts[\CURLOPT_NOSIGNAL] = \true;
        }
        if (\extension_loaded('zlib') && !isset($options['normalized_headers']['accept-encoding'])) {
            $options['headers'][] = 'Accept-Encoding: gzip';
        }
        $body = $options['body'];
        foreach ($options['headers'] as $i => $header) {
            if (\is_string($body) && '' !== $body && 0 === stripos($header, 'Content-Length: ')) {
                unset($options['headers'][$i]);
                continue;
            }
            if (':' === $header[-2] && \strlen($header) - 2 === strpos($header, ': ')) {
                $curlopts[\CURLOPT_HTTPHEADER][] = substr_replace($header, ';', -2);
            } else {
                $curlopts[\CURLOPT_HTTPHEADER][] = $header;
            }
        }
        foreach (['accept', 'expect'] as $header) {
            if (!isset($options['normalized_headers'][$header][0])) {
                $curlopts[\CURLOPT_HTTPHEADER][] = $header . ':';
            }
        }
        if (!\is_string($body)) {
            if (\is_resource($body)) {
                $curlopts[\CURLOPT_INFILE] = $body;
            } else {
                $eof = \false;
                $buffer = '';
                $curlopts[\CURLOPT_READFUNCTION] = static function ($ch, $fd, $length) use ($body, &$buffer, &$eof) {
                    return self::readRequestBody($length, $body, $buffer, $eof);
                };
            }
            if (isset($options['normalized_headers']['content-length'][0])) {
                $curlopts[\CURLOPT_INFILESIZE] = (int) substr($options['normalized_headers']['content-length'][0], \strlen('Content-Length: '));
            }
            if (!isset($options['normalized_headers']['transfer-encoding'])) {
                $curlopts[\CURLOPT_HTTPHEADER][] = 'Transfer-Encoding:' . (isset($curlopts[\CURLOPT_INFILESIZE]) ? '' : ' chunked');
            }
            if ('POST' !== $method) {
                $curlopts[\CURLOPT_UPLOAD] = \true;
                if (!isset($options['normalized_headers']['content-type']) && 0 !== ($curlopts[\CURLOPT_INFILESIZE] ?? null)) {
                    $curlopts[\CURLOPT_HTTPHEADER][] = 'Content-Type: application/x-www-form-urlencoded';
                }
            }
        } elseif ('' !== $body || 'POST' === $method) {
            $curlopts[\CURLOPT_POSTFIELDS] = $body;
        }
        if ($options['peer_fingerprint']) {
            if (!isset($options['peer_fingerprint']['pin-sha256'])) {
                throw new TransportException(__CLASS__ . ' supports only "pin-sha256" fingerprints.');
            }
            $curlopts[\CURLOPT_PINNEDPUBLICKEY] = 'sha256//' . implode(';sha256//', $options['peer_fingerprint']['pin-sha256']);
        }
        if ($options['bindto']) {
            if (file_exists($options['bindto'])) {
                $curlopts[\CURLOPT_UNIX_SOCKET_PATH] = $options['bindto'];
            } elseif (strncmp($options['bindto'], 'if!', strlen('if!')) !== 0 && preg_match('/^(.*):(\d+)$/', $options['bindto'], $matches)) {
                $curlopts[\CURLOPT_INTERFACE] = $matches[1];
                $curlopts[\CURLOPT_LOCALPORT] = $matches[2];
            } else {
                $curlopts[\CURLOPT_INTERFACE] = $options['bindto'];
            }
        }
        if (0 < $options['max_duration']) {
            $curlopts[\CURLOPT_TIMEOUT_MS] = 1000 * $options['max_duration'];
        }
        if (!empty($options['extra']['curl']) && \is_array($options['extra']['curl'])) {
            $this->validateExtraCurlOptions($options['extra']['curl']);
            $curlopts += $options['extra']['curl'];
        }
        if ($pushedResponse = $this->multi->pushedResponses[$url] ?? null) {
            unset($this->multi->pushedResponses[$url]);
            if (self::acceptPushForRequest($method, $options, $pushedResponse)) {
                ($nullsafeVariable1 = $this->logger) ? $nullsafeVariable1->debug(sprintf('Accepting pushed response: "%s %s"', $method, $url)) : null;
                $ch = $pushedResponse->handle;
                $pushedResponse = $pushedResponse->response;
                $pushedResponse->__construct($this->multi, $url, $options, $this->logger);
            } else {
                ($nullsafeVariable2 = $this->logger) ? $nullsafeVariable2->debug(sprintf('Rejecting pushed response: "%s"', $url)) : null;
                $pushedResponse = null;
            }
        }
        if (!$pushedResponse) {
            $ch = curl_init();
            ($nullsafeVariable3 = $this->logger) ? $nullsafeVariable3->info(sprintf('Request: "%s %s"', $method, $url)) : null;
            $curlopts += [\CURLOPT_SHARE => $this->multi->share];
        }
        foreach ($curlopts as $opt => $value) {
            if (null !== $value && !curl_setopt($ch, $opt, $value) && \CURLOPT_CERTINFO !== $opt && (!\defined('CURLOPT_HEADEROPT') || \CURLOPT_HEADEROPT !== $opt)) {
                $constantName = $this->findConstantName($opt);
                throw new TransportException(sprintf('Curl option "%s" is not supported.', $constantName ?? $opt));
            }
        }
        return $pushedResponse ?? new CurlResponse($this->multi, $ch, $options, $this->logger, $method, self::createRedirectResolver($options, $host, $port), CurlClientState::$curlVersion['version_number'], $url);
    }
    /**
     * @param ResponseInterface|iterable $responses
     * @param float|null $timeout
     */
    public function stream($responses, $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof CurlResponse) {
            $responses = [$responses];
        }
        if (is_resource($this->multi->handle) || $this->multi->handle instanceof CurlMultiHandle) {
            $active = 0;
            while (\CURLM_CALL_MULTI_PERFORM === curl_multi_exec($this->multi->handle, $active)) {
            }
        }
        return new ResponseStream(CurlResponse::stream($responses, $timeout));
    }
    public function reset()
    {
        $this->multi->reset();
    }
    private static function acceptPushForRequest(string $method, array $options, PushedResponse $pushedResponse): bool
    {
        if ('' !== $options['body'] || $method !== $pushedResponse->requestHeaders[':method'][0]) {
            return \false;
        }
        foreach (['proxy', 'no_proxy', 'bindto', 'local_cert', 'local_pk'] as $k) {
            if ($options[$k] !== $pushedResponse->parentOptions[$k]) {
                return \false;
            }
        }
        foreach (['authorization', 'cookie', 'range', 'proxy-authorization'] as $k) {
            $normalizedHeaders = $options['normalized_headers'][$k] ?? [];
            foreach ($normalizedHeaders as $i => $v) {
                $normalizedHeaders[$i] = substr($v, \strlen($k) + 2);
            }
            if (($pushedResponse->requestHeaders[$k] ?? []) !== $normalizedHeaders) {
                return \false;
            }
        }
        return \true;
    }
    private static function readRequestBody(int $length, Closure $body, string &$buffer, bool &$eof): string
    {
        if (!$eof && \strlen($buffer) < $length) {
            if (!\is_string($data = $body($length))) {
                throw new TransportException(sprintf('The return value of the "body" option callback must be a string, "%s" returned.', get_debug_type($data)));
            }
            $buffer .= $data;
            $eof = '' === $data;
        }
        $data = substr($buffer, 0, $length);
        $buffer = substr($buffer, $length);
        return $data;
    }
    private static function createRedirectResolver(array $options, string $host, int $port): Closure
    {
        $redirectHeaders = [];
        if (0 < $options['max_redirects']) {
            $redirectHeaders['host'] = $host;
            $redirectHeaders['port'] = $port;
            $redirectHeaders['with_auth'] = $redirectHeaders['no_auth'] = array_filter($options['headers'], static function ($h) {
                return 0 !== stripos($h, 'Host:');
            });
            if (isset($options['normalized_headers']['authorization'][0]) || isset($options['normalized_headers']['cookie'][0])) {
                $redirectHeaders['no_auth'] = array_filter($options['headers'], static function ($h) {
                    return 0 !== stripos($h, 'Authorization:') && 0 !== stripos($h, 'Cookie:');
                });
            }
        }
        return static function ($ch, string $location, bool $noContent) use (&$redirectHeaders, $options) {
            try {
                $location = self::parseUrl($location);
            } catch (InvalidArgumentException $exception) {
                return null;
            }
            if ($noContent && $redirectHeaders) {
                $filterContentHeaders = static function ($h) {
                    return 0 !== stripos($h, 'Content-Length:') && 0 !== stripos($h, 'Content-Type:') && 0 !== stripos($h, 'Transfer-Encoding:');
                };
                $redirectHeaders['no_auth'] = array_filter($redirectHeaders['no_auth'], $filterContentHeaders);
                $redirectHeaders['with_auth'] = array_filter($redirectHeaders['with_auth'], $filterContentHeaders);
            }
            if ($redirectHeaders && $host = parse_url('http:' . $location['authority'], \PHP_URL_HOST)) {
                $port = parse_url('http:' . $location['authority'], \PHP_URL_PORT) ?: (('http:' === $location['scheme']) ? 80 : 443);
                $requestHeaders = ($redirectHeaders['host'] === $host && $redirectHeaders['port'] === $port) ? $redirectHeaders['with_auth'] : $redirectHeaders['no_auth'];
                curl_setopt($ch, \CURLOPT_HTTPHEADER, $requestHeaders);
            } elseif ($noContent && $redirectHeaders) {
                curl_setopt($ch, \CURLOPT_HTTPHEADER, $redirectHeaders['with_auth']);
            }
            $url = self::parseUrl(curl_getinfo($ch, \CURLINFO_EFFECTIVE_URL));
            $url = self::resolveUrl($location, $url);
            curl_setopt($ch, \CURLOPT_PROXY, self::getProxyUrl($options['proxy'], $url));
            return implode('', $url);
        };
    }
    private function findConstantName(int $opt): ?string
    {
        $constants = array_filter(get_defined_constants(), static function ($v, $k) use ($opt) {
            return $v === $opt && 'C' === $k[0] && (strncmp($k, 'CURLOPT_', strlen('CURLOPT_')) === 0 || strncmp($k, 'CURLINFO_', strlen('CURLINFO_')) === 0);
        }, \ARRAY_FILTER_USE_BOTH);
        return key($constants);
    }
    private function validateExtraCurlOptions(array $options): void
    {
        $curloptsToConfig = [\CURLOPT_HTTPAUTH => 'auth_ntlm', \CURLOPT_USERPWD => 'auth_ntlm', \CURLOPT_RESOLVE => 'resolve', \CURLOPT_NOSIGNAL => 'timeout', \CURLOPT_HTTPHEADER => 'headers', \CURLOPT_INFILE => 'body', \CURLOPT_READFUNCTION => 'body', \CURLOPT_INFILESIZE => 'body', \CURLOPT_POSTFIELDS => 'body', \CURLOPT_UPLOAD => 'body', \CURLOPT_INTERFACE => 'bindto', \CURLOPT_TIMEOUT_MS => 'max_duration', \CURLOPT_TIMEOUT => 'max_duration', \CURLOPT_MAXREDIRS => 'max_redirects', \CURLOPT_POSTREDIR => 'max_redirects', \CURLOPT_PROXY => 'proxy', \CURLOPT_NOPROXY => 'no_proxy', \CURLOPT_SSL_VERIFYPEER => 'verify_peer', \CURLOPT_SSL_VERIFYHOST => 'verify_host', \CURLOPT_CAINFO => 'cafile', \CURLOPT_CAPATH => 'capath', \CURLOPT_SSL_CIPHER_LIST => 'ciphers', \CURLOPT_SSLCERT => 'local_cert', \CURLOPT_SSLKEY => 'local_pk', \CURLOPT_KEYPASSWD => 'passphrase', \CURLOPT_CERTINFO => 'capture_peer_cert_chain', \CURLOPT_USERAGENT => 'normalized_headers', \CURLOPT_REFERER => 'headers', \CURLOPT_NOPROGRESS => 'on_progress', \CURLOPT_PROGRESSFUNCTION => 'on_progress'];
        if (\defined('CURLOPT_UNIX_SOCKET_PATH')) {
            $curloptsToConfig[\CURLOPT_UNIX_SOCKET_PATH] = 'bindto';
        }
        if (\defined('CURLOPT_PINNEDPUBLICKEY')) {
            $curloptsToConfig[\CURLOPT_PINNEDPUBLICKEY] = 'peer_fingerprint';
        }
        $curloptsToCheck = [\CURLOPT_PRIVATE, \CURLOPT_HEADERFUNCTION, \CURLOPT_WRITEFUNCTION, \CURLOPT_VERBOSE, \CURLOPT_STDERR, \CURLOPT_RETURNTRANSFER, \CURLOPT_URL, \CURLOPT_FOLLOWLOCATION, \CURLOPT_HEADER, \CURLOPT_CONNECTTIMEOUT, \CURLOPT_CONNECTTIMEOUT_MS, \CURLOPT_HTTP_VERSION, \CURLOPT_PORT, \CURLOPT_DNS_USE_GLOBAL_CACHE, \CURLOPT_PROTOCOLS, \CURLOPT_REDIR_PROTOCOLS, \CURLOPT_COOKIEFILE, \CURLINFO_REDIRECT_COUNT];
        if (\defined('CURLOPT_HTTP09_ALLOWED')) {
            $curloptsToCheck[] = \CURLOPT_HTTP09_ALLOWED;
        }
        if (\defined('CURLOPT_HEADEROPT')) {
            $curloptsToCheck[] = \CURLOPT_HEADEROPT;
        }
        $methodOpts = [\CURLOPT_POST, \CURLOPT_PUT, \CURLOPT_CUSTOMREQUEST, \CURLOPT_HTTPGET, \CURLOPT_NOBODY];
        foreach ($options as $opt => $optValue) {
            if (isset($curloptsToConfig[$opt])) {
                $constName = $this->findConstantName($opt) ?? $opt;
                throw new InvalidArgumentException(sprintf('Cannot set "%s" with "extra.curl", use option "%s" instead.', $constName, $curloptsToConfig[$opt]));
            }
            if (\in_array($opt, $methodOpts)) {
                throw new InvalidArgumentException('The HTTP method cannot be overridden using "extra.curl".');
            }
            if (\in_array($opt, $curloptsToCheck)) {
                $constName = $this->findConstantName($opt) ?? $opt;
                throw new InvalidArgumentException(sprintf('Cannot set "%s" with "extra.curl".', $constName));
            }
        }
    }
}
