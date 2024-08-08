<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Response;

use Generator;
use Error;
use Closure;
use Throwable;
use InflateContext;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\DataChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\ErrorChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\FirstChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Chunk\LastChunk;
use Staatic\Vendor\Symfony\Component\HttpClient\Exception\TransportException;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\Canary;
use Staatic\Vendor\Symfony\Component\HttpClient\Internal\ClientState;
trait TransportResponseTrait
{
    /**
     * @var Canary
     */
    private $canary;
    /**
     * @var mixed[]
     */
    private $headers = [];
    /**
     * @var mixed[]
     */
    private $info = ['response_headers' => [], 'http_code' => 0, 'error' => null, 'canceled' => \false];
    private $handle;
    /**
     * @var int|string
     */
    private $id;
    /**
     * @var float|null
     */
    private $timeout = 0;
    /**
     * @var InflateContext|bool|null
     */
    private $inflate = null;
    /**
     * @var mixed[]|null
     */
    private $finalInfo;
    /**
     * @var LoggerInterface|null
     */
    private $logger;
    public function getStatusCode(): int
    {
        if ($this->initializer) {
            self::initialize($this);
        }
        return $this->info['http_code'];
    }
    /**
     * @param bool $throw
     */
    public function getHeaders($throw = \true): array
    {
        if ($this->initializer) {
            self::initialize($this);
        }
        if ($throw) {
            $this->checkStatusCode();
        }
        return $this->headers;
    }
    public function cancel(): void
    {
        $this->info['canceled'] = \true;
        $this->info['error'] = 'Response has been canceled.';
        $this->close();
    }
    protected function close(): void
    {
        $this->canary->cancel();
        $this->inflate = null;
    }
    /**
     * @param $this $response
     * @param mixed[] $runningResponses
     */
    abstract protected static function schedule($response, &$runningResponses): void;
    /**
     * @param ClientState $multi
     * @param mixed[] $responses
     */
    abstract protected static function perform($multi, &$responses): void;
    /**
     * @param ClientState $multi
     * @param float $timeout
     */
    abstract protected static function select($multi, $timeout): int;
    private static function addResponseHeaders(array $responseHeaders, array &$info, array &$headers, string &$debug = ''): void
    {
        foreach ($responseHeaders as $h) {
            if (11 <= \strlen($h) && '/' === $h[4] && preg_match('#^HTTP/\d+(?:\.\d+)? (\d\d\d)(?: |$)#', $h, $m)) {
                if ($headers) {
                    $debug .= "< \r\n";
                    $headers = [];
                }
                $info['http_code'] = (int) $m[1];
            } elseif (2 === \count($m = explode(':', $h, 2))) {
                $headers[strtolower($m[0])][] = ltrim($m[1]);
            }
            $debug .= "< {$h}\r\n";
            $info['response_headers'][] = $h;
        }
        $debug .= "< \r\n";
    }
    private function doDestruct(): void
    {
        $this->shouldBuffer = \true;
        if ($this->initializer && null === $this->info['error']) {
            self::initialize($this);
            $this->checkStatusCode();
        }
    }
    /**
     * @param iterable $responses
     * @param float|null $timeout
     */
    public static function stream($responses, $timeout = null): Generator
    {
        $runningResponses = [];
        foreach ($responses as $response) {
            self::schedule($response, $runningResponses);
        }
        $lastActivity = microtime(\true);
        $elapsedTimeout = 0;
        if ($fromLastTimeout = 0.0 === $timeout && '-0' === (string) $timeout) {
            $timeout = null;
        } elseif ($fromLastTimeout = 0 > $timeout) {
            $timeout = -$timeout;
        }
        while (\true) {
            $hasActivity = \false;
            $timeoutMax = 0;
            $timeoutMin = $timeout ?? \INF;
            foreach ($runningResponses as $i => [$multi]) {
                $responses =& $runningResponses[$i][1];
                self::perform($multi, $responses);
                foreach ($responses as $j => $response) {
                    $timeoutMax = $timeout ?? max($timeoutMax, $response->timeout);
                    $timeoutMin = min($timeoutMin, $response->timeout, 1);
                    $chunk = \false;
                    if ($fromLastTimeout && null !== $multi->lastTimeout) {
                        $elapsedTimeout = microtime(\true) - $multi->lastTimeout;
                    }
                    if (isset($multi->handlesActivity[$j])) {
                        $multi->lastTimeout = null;
                    } elseif (!isset($multi->openHandles[$j])) {
                        unset($responses[$j]);
                        continue;
                    } elseif ($elapsedTimeout >= $timeoutMax) {
                        $multi->handlesActivity[$j] = [new ErrorChunk($response->offset, sprintf('Idle timeout reached for "%s".', $response->getInfo('url')))];
                        $multi->lastTimeout = $multi->lastTimeout ?? $lastActivity;
                    } else {
                        continue;
                    }
                    while ($multi->handlesActivity[$j] ?? \false) {
                        $hasActivity = \true;
                        $elapsedTimeout = 0;
                        if (\is_string($chunk = array_shift($multi->handlesActivity[$j]))) {
                            if (null !== $response->inflate && \false === $chunk = @inflate_add($response->inflate, $chunk)) {
                                $multi->handlesActivity[$j] = [null, new TransportException(sprintf('Error while processing content unencoding for "%s".', $response->getInfo('url')))];
                                continue;
                            }
                            if ('' !== $chunk && null !== $response->content && \strlen($chunk) !== fwrite($response->content, $chunk)) {
                                $multi->handlesActivity[$j] = [null, new TransportException(sprintf('Failed writing %d bytes to the response buffer.', \strlen($chunk)))];
                                continue;
                            }
                            $chunkLen = \strlen($chunk);
                            $chunk = new DataChunk($response->offset, $chunk);
                            $response->offset += $chunkLen;
                        } elseif (null === $chunk) {
                            $e = $multi->handlesActivity[$j][0];
                            unset($responses[$j], $multi->handlesActivity[$j]);
                            $response->close();
                            if (null !== $e) {
                                $response->info['error'] = $e->getMessage();
                                if ($e instanceof Error) {
                                    throw $e;
                                }
                                $chunk = new ErrorChunk($response->offset, $e);
                            } else {
                                if (0 === $response->offset && null === $response->content) {
                                    $response->content = fopen('php://memory', 'w+');
                                }
                                $chunk = new LastChunk($response->offset);
                            }
                        } elseif ($chunk instanceof ErrorChunk) {
                            unset($responses[$j]);
                            $elapsedTimeout = $timeoutMax;
                        } elseif ($chunk instanceof FirstChunk) {
                            if ($response->logger) {
                                $info = $response->getInfo();
                                $response->logger->info(sprintf('Response: "%s %s"', $info['http_code'], $info['url']));
                            }
                            $response->inflate = (\extension_loaded('zlib') && $response->inflate && 'gzip' === ($response->headers['content-encoding'][0] ?? null)) ? inflate_init(\ZLIB_ENCODING_GZIP) : null;
                            if ($response->shouldBuffer instanceof Closure) {
                                try {
                                    $response->shouldBuffer = ($response->shouldBuffer)($response->headers);
                                    if (null !== $response->info['error']) {
                                        throw new TransportException($response->info['error']);
                                    }
                                } catch (Throwable $e) {
                                    $response->close();
                                    $multi->handlesActivity[$j] = [null, $e];
                                }
                            }
                            if (\true === $response->shouldBuffer) {
                                $response->content = fopen('php://temp', 'w+');
                            } elseif (\is_resource($response->shouldBuffer)) {
                                $response->content = $response->shouldBuffer;
                            }
                            $response->shouldBuffer = null;
                            yield $response => $chunk;
                            if ($response->initializer && null === $response->info['error']) {
                                $response->getHeaders(\true);
                            }
                            continue;
                        }
                        yield $response => $chunk;
                    }
                    unset($multi->handlesActivity[$j]);
                    if ($chunk instanceof ErrorChunk && !$chunk->didThrow()) {
                        $chunk->getContent();
                    }
                }
                if (!$responses) {
                    unset($runningResponses[$i]);
                }
                $multi->handlesActivity = $multi->handlesActivity ?: [];
                $multi->openHandles = $multi->openHandles ?: [];
            }
            if (!$runningResponses) {
                break;
            }
            if ($hasActivity) {
                $lastActivity = microtime(\true);
                continue;
            }
            if (-1 === self::select($multi, min($timeoutMin, $timeoutMax - $elapsedTimeout))) {
                usleep(min(500, 1000000.0 * $timeoutMin));
            }
            $elapsedTimeout = microtime(\true) - $lastActivity;
        }
    }
}
