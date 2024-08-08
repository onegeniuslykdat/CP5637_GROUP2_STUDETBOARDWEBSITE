<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Response;

use CurlHandle;
use Throwable;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\FirstChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\InformationalChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\Canary;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\ClientState;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\CurlClientState;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
final class CurlResponse implements ResponseInterface, StreamableInterface
{
    use CommonResponseTrait {
        getContent as private doGetContent;
    }
    use TransportResponseTrait;
    /**
     * @var CurlClientState
     */
    private $multi;
    private $debugBuffer;
    /**
     * @param CurlHandle|string $ch
     */
    public function __construct(CurlClientState $multi, $ch, array $options = null, LoggerInterface $logger = null, string $method = 'GET', callable $resolveRedirect = null, int $curlVersion = null, string $originalUrl = null)
    {
        $this->multi = $multi;
        if (is_resource($ch) || $ch instanceof CurlHandle) {
            $this->handle = $ch;
            $this->debugBuffer = fopen('php://temp', 'w+');
            if (0x74000 === $curlVersion) {
                fwrite($this->debugBuffer, 'Due to a bug in curl 7.64.0, the debug log is disabled; use another version to work around the issue.');
            } else {
                curl_setopt($ch, \CURLOPT_VERBOSE, \true);
                curl_setopt($ch, \CURLOPT_STDERR, $this->debugBuffer);
            }
        } else {
            $this->info['url'] = $ch;
            $ch = $this->handle;
        }
        $this->id = $id = (int) $ch;
        $this->logger = $logger;
        $this->shouldBuffer = $options['buffer'] ?? \true;
        $this->timeout = $options['timeout'] ?? null;
        $this->info['http_method'] = $method;
        $this->info['user_data'] = $options['user_data'] ?? null;
        $this->info['max_duration'] = $options['max_duration'] ?? null;
        $this->info['start_time'] = $this->info['start_time'] ?? microtime(\true);
        $this->info['original_url'] = $originalUrl ?? $this->info['url'] ?? curl_getinfo($ch, \CURLINFO_EFFECTIVE_URL);
        $info =& $this->info;
        $headers =& $this->headers;
        $debugBuffer = $this->debugBuffer;
        if (!$info['response_headers']) {
            curl_setopt($ch, \CURLOPT_PRIVATE, (\in_array($method, ['GET', 'HEAD', 'OPTIONS', 'TRACE'], \true) && 1.0 < (float) ($options['http_version'] ?? 1.1)) ? 'H2' : 'H0');
        }
        curl_setopt($ch, \CURLOPT_HEADERFUNCTION, static function ($ch, string $data) use (&$info, &$headers, $options, $multi, $id, &$location, $resolveRedirect, $logger): int {
            return self::parseHeaderLine($ch, $data, $info, $headers, $options, $multi, $id, $location, $resolveRedirect, $logger);
        });
        if (null === $options) {
            curl_setopt($ch, \CURLOPT_WRITEFUNCTION, static function ($ch, string $data) use ($multi, $id): int {
                $multi->handlesActivity[$id][] = $data;
                curl_pause($ch, \CURLPAUSE_RECV);
                return \strlen($data);
            });
            return;
        }
        $execCounter = $multi->execCounter;
        $this->info['pause_handler'] = static function (float $duration) use ($ch, $multi, $execCounter) {
            if (0 < $duration) {
                if ($execCounter === $multi->execCounter) {
                    $multi->execCounter = (!\is_float($execCounter)) ? 1 + $execCounter : \PHP_INT_MIN;
                    curl_multi_remove_handle($multi->handle, $ch);
                }
                $lastExpiry = end($multi->pauseExpiries);
                $multi->pauseExpiries[(int) $ch] = $duration += microtime(\true);
                if (\false !== $lastExpiry && $lastExpiry > $duration) {
                    asort($multi->pauseExpiries);
                }
                curl_pause($ch, \CURLPAUSE_ALL);
            } else {
                unset($multi->pauseExpiries[(int) $ch]);
                curl_pause($ch, \CURLPAUSE_CONT);
                curl_multi_add_handle($multi->handle, $ch);
            }
        };
        $this->inflate = !isset($options['normalized_headers']['accept-encoding']);
        curl_pause($ch, \CURLPAUSE_CONT);
        if ($onProgress = $options['on_progress']) {
            $url = isset($info['url']) ? ['url' => $info['url']] : [];
            curl_setopt($ch, \CURLOPT_NOPROGRESS, \false);
            curl_setopt($ch, \CURLOPT_PROGRESSFUNCTION, static function ($ch, $dlSize, $dlNow) use ($onProgress, &$info, $url, $multi, $debugBuffer) {
                try {
                    rewind($debugBuffer);
                    $debug = ['debug' => stream_get_contents($debugBuffer)];
                    $onProgress($dlNow, $dlSize, $url + curl_getinfo($ch) + $info + $debug);
                } catch (Throwable $e) {
                    $multi->handlesActivity[(int) $ch][] = null;
                    $multi->handlesActivity[(int) $ch][] = $e;
                    return 1;
                }
                return null;
            });
        }
        curl_setopt($ch, \CURLOPT_WRITEFUNCTION, static function ($ch, string $data) use ($multi, $id): int {
            if ('H' === (curl_getinfo($ch, \CURLINFO_PRIVATE)[0] ?? null)) {
                $multi->handlesActivity[$id][] = null;
                $multi->handlesActivity[$id][] = new TransportException(sprintf('Unsupported protocol for "%s"', curl_getinfo($ch, \CURLINFO_EFFECTIVE_URL)));
                return 0;
            }
            curl_setopt($ch, \CURLOPT_WRITEFUNCTION, static function ($ch, string $data) use ($multi, $id): int {
                $multi->handlesActivity[$id][] = $data;
                return \strlen($data);
            });
            $multi->handlesActivity[$id][] = $data;
            return \strlen($data);
        });
        $this->initializer = static function (self $response) {
            $waitFor = curl_getinfo($ch = $response->handle, \CURLINFO_PRIVATE);
            return 'H' === $waitFor[0];
        };
        $multi->lastTimeout = null;
        $multi->openHandles[$id] = [$ch, $options];
        curl_multi_add_handle($multi->handle, $ch);
        $this->canary = new Canary(static function () use ($ch, $multi, $id) {
            unset($multi->pauseExpiries[$id], $multi->openHandles[$id], $multi->handlesActivity[$id]);
            curl_setopt($ch, \CURLOPT_PRIVATE, '_0');
            if ($multi->performing) {
                return;
            }
            curl_multi_remove_handle($multi->handle, $ch);
            curl_setopt_array($ch, [\CURLOPT_NOPROGRESS => \true, \CURLOPT_PROGRESSFUNCTION => null, \CURLOPT_HEADERFUNCTION => null, \CURLOPT_WRITEFUNCTION => null, \CURLOPT_READFUNCTION => null, \CURLOPT_INFILE => null]);
            if (!$multi->openHandles) {
                $multi->dnsCache->evictions = $multi->dnsCache->evictions ?: $multi->dnsCache->removals;
                $multi->dnsCache->removals = $multi->dnsCache->hostnames = [];
            }
        });
    }
    /**
     * @param string|null $type
     * @return mixed
     */
    public function getInfo($type = null)
    {
        if (!$info = $this->finalInfo) {
            $info = array_merge($this->info, curl_getinfo($this->handle));
            $info['url'] = $this->info['url'] ?? $info['url'];
            $info['redirect_url'] = $this->info['redirect_url'] ?? null;
            if (isset($this->info['url']) && $info['start_time'] / 1000 < $info['total_time']) {
                $info['total_time'] -= $info['starttransfer_time'] ?: $info['total_time'];
                $info['starttransfer_time'] = 0.0;
            }
            rewind($this->debugBuffer);
            $info['debug'] = stream_get_contents($this->debugBuffer);
            $waitFor = curl_getinfo($this->handle, \CURLINFO_PRIVATE);
            if ('H' !== $waitFor[0] && 'C' !== $waitFor[0]) {
                curl_setopt($this->handle, \CURLOPT_VERBOSE, \false);
                rewind($this->debugBuffer);
                ftruncate($this->debugBuffer, 0);
                $this->finalInfo = $info;
            }
        }
        return (null !== $type) ? $info[$type] ?? null : $info;
    }
    /**
     * @param bool $throw
     */
    public function getContent($throw = \true): string
    {
        $performing = $this->multi->performing;
        $this->multi->performing = $performing || '_0' === curl_getinfo($this->handle, \CURLINFO_PRIVATE);
        try {
            return $this->doGetContent($throw);
        } finally {
            $this->multi->performing = $performing;
        }
    }
    public function __destruct()
    {
        try {
            if (null === $this->timeout) {
                return;
            }
            $this->doDestruct();
        } finally {
            if (\is_resource($this->handle) || $this->handle instanceof CurlHandle) {
                curl_setopt($this->handle, \CURLOPT_VERBOSE, \false);
            }
        }
    }
    private static function schedule($response, &$runningResponses): void
    {
        if (isset($runningResponses[$i = (int) $response->multi->handle])) {
            $runningResponses[$i][1][$response->id] = $response;
        } else {
            $runningResponses[$i] = [$response->multi, [$response->id => $response]];
        }
        if ('_0' === curl_getinfo($ch = $response->handle, \CURLINFO_PRIVATE)) {
            $response->multi->handlesActivity[$response->id][] = null;
            $response->multi->handlesActivity[$response->id][] = (null !== $response->info['error']) ? new TransportException($response->info['error']) : null;
        }
    }
    private static function perform($multi, &$responses = null): void
    {
        if ($multi->performing) {
            if ($responses) {
                $response = current($responses);
                $multi->handlesActivity[(int) $response->handle][] = null;
                $multi->handlesActivity[(int) $response->handle][] = new TransportException(sprintf('Userland callback cannot use the client nor the response while processing "%s".', curl_getinfo($response->handle, \CURLINFO_EFFECTIVE_URL)));
            }
            return;
        }
        try {
            $multi->performing = \true;
            ++$multi->execCounter;
            $active = 0;
            while (\CURLM_CALL_MULTI_PERFORM === $err = curl_multi_exec($multi->handle, $active)) {
            }
            if (\CURLM_OK !== $err) {
                throw new TransportException(curl_multi_strerror($err));
            }
            while ($info = curl_multi_info_read($multi->handle)) {
                if (\CURLMSG_DONE !== $info['msg']) {
                    continue;
                }
                $result = $info['result'];
                $id = (int) $ch = $info['handle'];
                $waitFor = (@curl_getinfo($ch, \CURLINFO_PRIVATE)) ?: '_0';
                if (\in_array($result, [\CURLE_SEND_ERROR, \CURLE_RECV_ERROR, 16, 92], \true) && $waitFor[1] && 'C' !== $waitFor[0]) {
                    curl_multi_remove_handle($multi->handle, $ch);
                    $waitFor[1] = (string) ((int) $waitFor[1] - 1);
                    curl_setopt($ch, \CURLOPT_PRIVATE, $waitFor);
                    curl_setopt($ch, \CURLOPT_FORBID_REUSE, \true);
                    if (0 === curl_multi_add_handle($multi->handle, $ch)) {
                        continue;
                    }
                }
                if (\CURLE_RECV_ERROR === $result && 'H' === $waitFor[0] && 400 <= ($responses[(int) $ch]->info['http_code'] ?? 0)) {
                    $multi->handlesActivity[$id][] = new FirstChunk();
                }
                $multi->handlesActivity[$id][] = null;
                $multi->handlesActivity[$id][] = (\in_array($result, [\CURLE_OK, \CURLE_TOO_MANY_REDIRECTS], \true) || '_0' === $waitFor || curl_getinfo($ch, \CURLINFO_SIZE_DOWNLOAD) === curl_getinfo($ch, \CURLINFO_CONTENT_LENGTH_DOWNLOAD)) ? null : new TransportException(ucfirst(curl_error($ch) ?: curl_strerror($result)) . sprintf(' for "%s".', curl_getinfo($ch, \CURLINFO_EFFECTIVE_URL)));
            }
        } finally {
            $multi->performing = \false;
        }
    }
    private static function select($multi, $timeout): int
    {
        if ($multi->pauseExpiries) {
            $now = microtime(\true);
            foreach ($multi->pauseExpiries as $id => $pauseExpiry) {
                if ($now < $pauseExpiry) {
                    $timeout = min($timeout, $pauseExpiry - $now);
                    break;
                }
                unset($multi->pauseExpiries[$id]);
                curl_pause($multi->openHandles[$id][0], \CURLPAUSE_CONT);
                curl_multi_add_handle($multi->handle, $multi->openHandles[$id][0]);
            }
        }
        if (0 !== $selected = curl_multi_select($multi->handle, $timeout)) {
            return $selected;
        }
        if ($multi->pauseExpiries && 0 < $timeout -= microtime(\true) - $now) {
            usleep((int) (1000000.0 * $timeout));
        }
        return 0;
    }
    private static function parseHeaderLine($ch, string $data, array &$info, array &$headers, ?array $options, CurlClientState $multi, int $id, ?string &$location, ?callable $resolveRedirect, ?LoggerInterface $logger): int
    {
        if (substr_compare($data, "\r\n", -strlen("\r\n")) !== 0) {
            return 0;
        }
        $waitFor = (@curl_getinfo($ch, \CURLINFO_PRIVATE)) ?: '_0';
        if ('H' !== $waitFor[0]) {
            return \strlen($data);
        }
        $statusCode = curl_getinfo($ch, \CURLINFO_RESPONSE_CODE);
        if ($statusCode !== $info['http_code'] && !preg_match("#^HTTP/\\d+(?:\\.\\d+)? {$statusCode}(?: |\r\n\$)#", $data)) {
            return \strlen($data);
        }
        if ("\r\n" !== $data) {
            self::addResponseHeaders([substr($data, 0, -2)], $info, $headers);
            if (strncmp($data, 'HTTP/', strlen('HTTP/')) !== 0) {
                if (0 === stripos($data, 'Location:')) {
                    $location = trim(substr($data, 9, -2));
                }
                return \strlen($data);
            }
            if (\function_exists('openssl_x509_read') && $certinfo = curl_getinfo($ch, \CURLINFO_CERTINFO)) {
                $info['peer_certificate_chain'] = array_map('openssl_x509_read', array_column($certinfo, 'Cert'));
            }
            if (300 <= $info['http_code'] && $info['http_code'] < 400) {
                if (curl_getinfo($ch, \CURLINFO_REDIRECT_COUNT) === $options['max_redirects']) {
                    curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, \false);
                } elseif (303 === $info['http_code'] || 'POST' === $info['http_method'] && \in_array($info['http_code'], [301, 302], \true)) {
                    curl_setopt($ch, \CURLOPT_POSTFIELDS, '');
                }
            }
            return \strlen($data);
        }
        if (200 > $statusCode) {
            $multi->handlesActivity[$id][] = new InformationalChunk($statusCode, $headers);
            $location = null;
            return \strlen($data);
        }
        $info['redirect_url'] = null;
        if (300 <= $statusCode && $statusCode < 400 && null !== $location) {
            if ($noContent = 303 === $statusCode || 'POST' === $info['http_method'] && \in_array($statusCode, [301, 302], \true)) {
                $info['http_method'] = ('HEAD' === $info['http_method']) ? 'HEAD' : 'GET';
                curl_setopt($ch, \CURLOPT_CUSTOMREQUEST, $info['http_method']);
            }
            if (null === $info['redirect_url'] = $resolveRedirect($ch, $location, $noContent)) {
                $options['max_redirects'] = curl_getinfo($ch, \CURLINFO_REDIRECT_COUNT);
                curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, \false);
                curl_setopt($ch, \CURLOPT_MAXREDIRS, $options['max_redirects']);
            } else {
                $url = parse_url($location ?? ':');
                if (isset($url['host']) && null !== $ip = $multi->dnsCache->hostnames[$url['host'] = strtolower($url['host'])] ?? null) {
                    $port = $url['port'] ?? (('http' === ($url['scheme'] ?? parse_url(curl_getinfo($ch, \CURLINFO_EFFECTIVE_URL), \PHP_URL_SCHEME))) ? 80 : 443);
                    curl_setopt($ch, \CURLOPT_RESOLVE, ["{$url['host']}:{$port}:{$ip}"]);
                    $multi->dnsCache->removals["-{$url['host']}:{$port}"] = "-{$url['host']}:{$port}";
                }
            }
        }
        if (401 === $statusCode && isset($options['auth_ntlm']) && 0 === strncasecmp($headers['www-authenticate'][0] ?? '', 'NTLM ', 5)) {
        } elseif ($statusCode < 300 || 400 <= $statusCode || null === $location || curl_getinfo($ch, \CURLINFO_REDIRECT_COUNT) === $options['max_redirects']) {
            $multi->handlesActivity[$id][] = new FirstChunk();
            if ('HEAD' === $info['http_method'] || \in_array($statusCode, [204, 304], \true)) {
                $waitFor = '_0';
                $multi->handlesActivity[$id][] = null;
                $multi->handlesActivity[$id][] = null;
            } else {
                $waitFor[0] = 'C';
            }
            curl_setopt($ch, \CURLOPT_PRIVATE, $waitFor);
        } elseif (null !== $info['redirect_url'] && $logger) {
            $logger->info(sprintf('Redirecting: "%s %s"', $info['http_code'], $info['redirect_url']));
        }
        $location = null;
        return \strlen($data);
    }
}
