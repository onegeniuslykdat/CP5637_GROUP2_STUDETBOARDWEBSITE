<?php

namespace Staatic\Vendor\Symfony\Contracts\HttpClient;

use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Test\HttpClientTestCase;
interface HttpClientInterface
{
    public const OPTIONS_DEFAULTS = ['auth_basic' => null, 'auth_bearer' => null, 'query' => [], 'headers' => [], 'body' => '', 'json' => null, 'user_data' => null, 'max_redirects' => 20, 'http_version' => null, 'base_uri' => null, 'buffer' => \true, 'on_progress' => null, 'resolve' => [], 'proxy' => null, 'no_proxy' => null, 'timeout' => null, 'max_duration' => 0, 'bindto' => '0', 'verify_peer' => \true, 'verify_host' => \true, 'cafile' => null, 'capath' => null, 'local_cert' => null, 'local_pk' => null, 'passphrase' => null, 'ciphers' => null, 'peer_fingerprint' => null, 'capture_peer_cert_chain' => \false, 'crypto_method' => \STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT, 'extra' => []];
    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    public function request($method, $url, $options = []): ResponseInterface;
    /**
     * @param ResponseInterface|iterable $responses
     * @param float|null $timeout
     */
    public function stream($responses, $timeout = null): ResponseStreamInterface;
    /**
     * @param mixed[] $options
     * @return static
     */
    public function withOptions($options);
}
