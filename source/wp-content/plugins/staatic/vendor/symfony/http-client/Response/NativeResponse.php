<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Response;

use Throwable;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\FirstChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\Canary;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\ClientState;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\NativeClientState;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
final class NativeResponse implements ResponseInterface, StreamableInterface
{
    use CommonResponseTrait;
    use TransportResponseTrait;
    private $context;
    /**
     * @var string
     */
    private $url;
    private $resolver;
    private $onProgress;
    /**
     * @var int|null
     */
    private $remaining;
    private $buffer;
    /**
     * @var NativeClientState
     */
    private $multi;
    /**
     * @var float
     */
    private $pauseExpiry = 0.0;
    public function __construct(NativeClientState $multi, $context, string $url, array $options, array &$info, callable $resolver, ?callable $onProgress, ?LoggerInterface $logger)
    {
        $this->multi = $multi;
        $this->id = $id = (int) $context;
        $this->context = $context;
        $this->url = $url;
        $this->logger = $logger;
        $this->timeout = $options['timeout'];
        $this->info =& $info;
        $this->resolver = $resolver;
        $this->onProgress = $onProgress;
        $this->inflate = !isset($options['normalized_headers']['accept-encoding']);
        $this->shouldBuffer = $options['buffer'] ?? \true;
        $this->buffer = fopen('php://temp', 'w+');
        $info['original_url'] = implode('', $info['url']);
        $info['user_data'] = $options['user_data'];
        $info['max_duration'] = $options['max_duration'];
        ++$multi->responseCount;
        $this->initializer = static function (self $response) {
            return null === $response->remaining;
        };
        $pauseExpiry =& $this->pauseExpiry;
        $info['pause_handler'] = static function (float $duration) use (&$pauseExpiry) {
            $pauseExpiry = (0 < $duration) ? microtime(\true) + $duration : 0;
        };
        $this->canary = new Canary(static function () use ($multi, $id) {
            if (null !== ($host = $multi->openHandles[$id][6] ?? null) && 0 >= --$multi->hosts[$host]) {
                unset($multi->hosts[$host]);
            }
            unset($multi->openHandles[$id], $multi->handlesActivity[$id]);
        });
    }
    /**
     * @param string|null $type
     * @return mixed
     */
    public function getInfo($type = null)
    {
        if (!$info = $this->finalInfo) {
            $info = $this->info;
            $info['url'] = implode('', $info['url']);
            unset($info['size_body'], $info['request_header']);
            if (null === $this->buffer) {
                $this->finalInfo = $info;
            }
        }
        return (null !== $type) ? $info[$type] ?? null : $info;
    }
    public function __destruct()
    {
        try {
            $this->doDestruct();
        } finally {
            if (0 >= --$this->multi->responseCount) {
                $this->multi->responseCount = 0;
                $this->multi->dnsCache = [];
            }
        }
    }
    private function open(): void
    {
        $url = $this->url;
        set_error_handler(function ($type, $msg) use (&$url) {
            if (\E_NOTICE !== $type || 'fopen(): Content-type not specified assuming application/x-www-form-urlencoded' !== $msg) {
                throw new TransportException($msg);
            }
            ($nullsafeVariable1 = $this->logger) ? $nullsafeVariable1->info(sprintf('%s for "%s".', $msg, $url ?? $this->url)) : null;
        });
        try {
            $this->info['start_time'] = microtime(\true);
            [$resolver, $url] = ($this->resolver)($this->multi);
            while (\true) {
                $context = stream_context_get_options($this->context);
                if ($proxy = $context['http']['proxy'] ?? null) {
                    $this->info['debug'] .= "* Establish HTTP proxy tunnel to {$proxy}\n";
                    $this->info['request_header'] = $url;
                } else {
                    $this->info['debug'] .= "*   Trying {$this->info['primary_ip']}...\n";
                    $this->info['request_header'] = $this->info['url']['path'] . $this->info['url']['query'];
                }
                $this->info['request_header'] = sprintf("> %s %s HTTP/%s \r\n", $context['http']['method'], $this->info['request_header'], $context['http']['protocol_version']);
                $this->info['request_header'] .= implode("\r\n", $context['http']['header']) . "\r\n\r\n";
                if (\array_key_exists('peer_name', $context['ssl']) && null === $context['ssl']['peer_name']) {
                    unset($context['ssl']['peer_name']);
                    $this->context = stream_context_create([], ['options' => $context] + stream_context_get_params($this->context));
                }
                $this->handle = $h = fopen($url, 'r', \false, $this->context);
                self::addResponseHeaders(stream_get_meta_data($h)['wrapper_data'], $this->info, $this->headers, $this->info['debug']);
                $url = $resolver($this->multi, $this->headers['location'][0] ?? null, $this->context);
                if (null === $url) {
                    break;
                }
                ($nullsafeVariable2 = $this->logger) ? $nullsafeVariable2->info(sprintf('Redirecting: "%s %s"', $this->info['http_code'], $url ?? $this->url)) : null;
            }
        } catch (Throwable $e) {
            $this->close();
            $this->multi->handlesActivity[$this->id][] = null;
            $this->multi->handlesActivity[$this->id][] = $e;
            return;
        } finally {
            $this->info['pretransfer_time'] = $this->info['total_time'] = microtime(\true) - $this->info['start_time'];
            restore_error_handler();
        }
        if (isset($context['ssl']['capture_peer_cert_chain']) && isset(($context = stream_context_get_options($this->context))['ssl']['peer_certificate_chain'])) {
            $this->info['peer_certificate_chain'] = $context['ssl']['peer_certificate_chain'];
        }
        stream_set_blocking($h, \false);
        $this->context = $this->resolver = null;
        if (isset($this->headers['content-length'])) {
            $this->remaining = (int) $this->headers['content-length'][0];
        } elseif ('chunked' === ($this->headers['transfer-encoding'][0] ?? null)) {
            stream_filter_append($this->buffer, 'dechunk', \STREAM_FILTER_WRITE);
            $this->remaining = -1;
        } else {
            $this->remaining = -2;
        }
        $this->multi->handlesActivity[$this->id] = [new FirstChunk()];
        if ('HEAD' === $context['http']['method'] || \in_array($this->info['http_code'], [204, 304], \true)) {
            $this->multi->handlesActivity[$this->id][] = null;
            $this->multi->handlesActivity[$this->id][] = null;
            return;
        }
        $host = parse_url($this->info['redirect_url'] ?? $this->url, \PHP_URL_HOST);
        $this->multi->lastTimeout = null;
        $this->multi->openHandles[$this->id] = [&$this->pauseExpiry, $h, $this->buffer, $this->onProgress, &$this->remaining, &$this->info, $host];
        $this->multi->hosts[$host] = 1 + ($this->multi->hosts[$host] ?? 0);
    }
    private function close(): void
    {
        $this->canary->cancel();
        $this->handle = $this->buffer = $this->inflate = $this->onProgress = null;
    }
    private static function schedule(self $response, array &$runningResponses): void
    {
        if (!isset($runningResponses[$i = $response->multi->id])) {
            $runningResponses[$i] = [$response->multi, []];
        }
        $runningResponses[$i][1][$response->id] = $response;
        if (null === $response->buffer) {
            $response->multi->handlesActivity[$response->id][] = null;
            $response->multi->handlesActivity[$response->id][] = (null !== $response->info['error']) ? new TransportException($response->info['error']) : null;
        }
    }
    private static function perform(ClientState $multi, array &$responses = null): void
    {
        foreach ($multi->openHandles as $i => [$pauseExpiry, $h, $buffer, $onProgress]) {
            if ($pauseExpiry) {
                if (microtime(\true) < $pauseExpiry) {
                    continue;
                }
                $multi->openHandles[$i][0] = 0;
            }
            $hasActivity = \false;
            $remaining =& $multi->openHandles[$i][4];
            $info =& $multi->openHandles[$i][5];
            $e = null;
            try {
                if ($remaining && '' !== $data = (string) fread($h, (0 > $remaining) ? 16372 : $remaining)) {
                    fwrite($buffer, $data);
                    $hasActivity = \true;
                    $multi->sleep = \false;
                    if (-1 !== $remaining) {
                        $remaining -= \strlen($data);
                    }
                }
            } catch (Throwable $e) {
                $hasActivity = $onProgress = \false;
            }
            if (!$hasActivity) {
                if ($onProgress) {
                    try {
                        $info['total_time'] = microtime(\true) - $info['start_time'];
                        $onProgress();
                    } catch (Throwable $e) {
                    }
                }
            } elseif ('' !== $data = stream_get_contents($buffer, -1, 0)) {
                rewind($buffer);
                ftruncate($buffer, 0);
                if (null === $e) {
                    $multi->handlesActivity[$i][] = $data;
                }
            }
            if (null !== $e || !$remaining || feof($h)) {
                $info['total_time'] = microtime(\true) - $info['start_time'];
                $info['starttransfer_time'] = $info['starttransfer_time'] ?: $info['total_time'];
                if ($onProgress) {
                    try {
                        $onProgress(-1);
                    } catch (Throwable $e) {
                    }
                }
                if (null === $e) {
                    if (0 < $remaining) {
                        $e = new TransportException(sprintf('Transfer closed with %s bytes remaining to read.', $remaining));
                    } elseif (-1 === $remaining && fwrite($buffer, '-') && '' !== stream_get_contents($buffer, -1, 0)) {
                        $e = new TransportException('Transfer closed with outstanding data remaining from chunked response.');
                    }
                }
                $multi->handlesActivity[$i][] = null;
                $multi->handlesActivity[$i][] = $e;
                if (null !== ($host = $multi->openHandles[$i][6] ?? null) && 0 >= --$multi->hosts[$host]) {
                    unset($multi->hosts[$host]);
                }
                unset($multi->openHandles[$i]);
                $multi->sleep = \false;
            }
        }
        if (null === $responses) {
            return;
        }
        $maxHosts = $multi->maxHostConnections;
        foreach ($responses as $i => $response) {
            if (null !== $response->remaining || null === $response->buffer) {
                continue;
            }
            if ($response->pauseExpiry && microtime(\true) < $response->pauseExpiry) {
                $multi->openHandles[$i] = [\INF, null, null, null];
            } elseif ($maxHosts && $maxHosts > ($multi->hosts[parse_url($response->url, \PHP_URL_HOST)] ?? 0)) {
                $response->open();
                $multi->sleep = \false;
                self::perform($multi);
                $maxHosts = 0;
            }
        }
    }
    private static function select(ClientState $multi, float $timeout): int
    {
        if (!$multi->sleep = !$multi->sleep) {
            return -1;
        }
        $_ = $handles = [];
        $now = null;
        foreach ($multi->openHandles as [$pauseExpiry, $h]) {
            if (null === $h) {
                continue;
            }
            if ($pauseExpiry && ($now = $now ?? microtime(\true)) < $pauseExpiry) {
                $timeout = min($timeout, $pauseExpiry - $now);
                continue;
            }
            $handles[] = $h;
        }
        if (!$handles) {
            usleep((int) (1000000.0 * $timeout));
            return 0;
        }
        return stream_select($handles, $_, $_, (int) $timeout, (int) (1000000.0 * ($timeout - (int) $timeout)));
    }
}
