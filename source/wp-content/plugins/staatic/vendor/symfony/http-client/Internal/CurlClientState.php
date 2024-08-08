<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Internal;

use CurlMultiHandle;
use CurlShareHandle;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Symfony\Component\HttpClient\Response\CurlResponse;
final class CurlClientState extends ClientState
{
    /**
     * @var CurlMultiHandle|null
     */
    public $handle;
    /**
     * @var CurlShareHandle|null
     */
    public $share;
    /**
     * @var bool
     */
    public $performing = \false;
    /**
     * @var mixed[]
     */
    public $pushedResponses = [];
    /**
     * @var DnsCache
     */
    public $dnsCache;
    /**
     * @var mixed[]
     */
    public $pauseExpiries = [];
    /**
     * @var int
     */
    public $execCounter = \PHP_INT_MIN;
    /**
     * @var LoggerInterface|null
     */
    public $logger;
    /**
     * @var mixed[]
     */
    public static $curlVersion;
    public function __construct(int $maxHostConnections, int $maxPendingPushes)
    {
        self::$curlVersion = self::$curlVersion ?? curl_version();
        $this->handle = curl_multi_init();
        $this->dnsCache = new DnsCache();
        $this->reset();
        if (\defined('CURLPIPE_MULTIPLEX')) {
            curl_multi_setopt($this->handle, \CURLMOPT_PIPELINING, \CURLPIPE_MULTIPLEX);
        }
        if (\defined('CURLMOPT_MAX_HOST_CONNECTIONS')) {
            $maxHostConnections = curl_multi_setopt($this->handle, \CURLMOPT_MAX_HOST_CONNECTIONS, (0 < $maxHostConnections) ? $maxHostConnections : \PHP_INT_MAX) ? 0 : $maxHostConnections;
        }
        if (\defined('CURLMOPT_MAXCONNECTS') && 0 < $maxHostConnections) {
            curl_multi_setopt($this->handle, \CURLMOPT_MAXCONNECTS, $maxHostConnections);
        }
        if (0 >= $maxPendingPushes) {
            return;
        }
        if (!\defined('CURLMOPT_PUSHFUNCTION') || 0x73d00 > self::$curlVersion['version_number'] || !(\CURL_VERSION_HTTP2 & self::$curlVersion['features'])) {
            return;
        }
        $multi = clone $this;
        $multi->handle = null;
        $multi->share = null;
        $multi->pushedResponses =& $this->pushedResponses;
        $multi->logger =& $this->logger;
        $multi->handlesActivity =& $this->handlesActivity;
        $multi->openHandles =& $this->openHandles;
        curl_multi_setopt($this->handle, \CURLMOPT_PUSHFUNCTION, static function ($parent, $pushed, array $requestHeaders) use ($multi, $maxPendingPushes) {
            return $multi->handlePush($parent, $pushed, $requestHeaders, $maxPendingPushes);
        });
    }
    public function reset()
    {
        foreach ($this->pushedResponses as $url => $response) {
            ($nullsafeVariable1 = $this->logger) ? $nullsafeVariable1->debug(sprintf('Unused pushed response: "%s"', $url)) : null;
            curl_multi_remove_handle($this->handle, $response->handle);
            curl_close($response->handle);
        }
        $this->pushedResponses = [];
        $this->dnsCache->evictions = $this->dnsCache->evictions ?: $this->dnsCache->removals;
        $this->dnsCache->removals = $this->dnsCache->hostnames = [];
        $this->share = curl_share_init();
        curl_share_setopt($this->share, \CURLSHOPT_SHARE, \CURL_LOCK_DATA_DNS);
        curl_share_setopt($this->share, \CURLSHOPT_SHARE, \CURL_LOCK_DATA_SSL_SESSION);
        if (\defined('CURL_LOCK_DATA_CONNECT') && \PHP_VERSION_ID >= 80000) {
            curl_share_setopt($this->share, \CURLSHOPT_SHARE, \CURL_LOCK_DATA_CONNECT);
        }
    }
    private function handlePush($parent, $pushed, array $requestHeaders, int $maxPendingPushes): int
    {
        $headers = [];
        $origin = curl_getinfo($parent, \CURLINFO_EFFECTIVE_URL);
        foreach ($requestHeaders as $h) {
            if (\false !== $i = strpos($h, ':', 1)) {
                $headers[substr($h, 0, $i)][] = substr($h, 1 + $i);
            }
        }
        if (!isset($headers[':method']) || !isset($headers[':scheme']) || !isset($headers[':authority']) || !isset($headers[':path'])) {
            ($nullsafeVariable2 = $this->logger) ? $nullsafeVariable2->debug(sprintf('Rejecting pushed response from "%s": pushed headers are invalid', $origin)) : null;
            return \CURL_PUSH_DENY;
        }
        $url = $headers[':scheme'][0] . '://' . $headers[':authority'][0];
        if (strncmp($origin, $url . '/', strlen($url . '/')) !== 0) {
            ($nullsafeVariable3 = $this->logger) ? $nullsafeVariable3->debug(sprintf('Rejecting pushed response from "%s": server is not authoritative for "%s"', $origin, $url)) : null;
            return \CURL_PUSH_DENY;
        }
        if ($maxPendingPushes <= \count($this->pushedResponses)) {
            $fifoUrl = key($this->pushedResponses);
            unset($this->pushedResponses[$fifoUrl]);
            ($nullsafeVariable4 = $this->logger) ? $nullsafeVariable4->debug(sprintf('Evicting oldest pushed response: "%s"', $fifoUrl)) : null;
        }
        $url .= $headers[':path'][0];
        ($nullsafeVariable5 = $this->logger) ? $nullsafeVariable5->debug(sprintf('Queueing pushed response: "%s"', $url)) : null;
        $this->pushedResponses[$url] = new PushedResponse(new CurlResponse($this, $pushed), $headers, $this->openHandles[(int) $parent][1] ?? [], $pushed);
        return \CURL_PUSH_OK;
    }
}
