<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
class HttpOptions
{
    /**
     * @var mixed[]
     */
    private $options = [];
    public function toArray(): array
    {
        return $this->options;
    }
    /**
     * @param string $user
     * @param string $password
     * @return static
     */
    public function setAuthBasic($user, $password = '')
    {
        $this->options['auth_basic'] = $user;
        if ('' !== $password) {
            $this->options['auth_basic'] .= ':' . $password;
        }
        return $this;
    }
    /**
     * @param string $token
     * @return static
     */
    public function setAuthBearer($token)
    {
        $this->options['auth_bearer'] = $token;
        return $this;
    }
    /**
     * @param mixed[] $query
     * @return static
     */
    public function setQuery($query)
    {
        $this->options['query'] = $query;
        return $this;
    }
    /**
     * @param iterable $headers
     * @return static
     */
    public function setHeaders($headers)
    {
        $this->options['headers'] = $headers;
        return $this;
    }
    /**
     * @param mixed $body
     * @return static
     */
    public function setBody($body)
    {
        $this->options['body'] = $body;
        return $this;
    }
    /**
     * @param mixed $json
     * @return static
     */
    public function setJson($json)
    {
        $this->options['json'] = $json;
        return $this;
    }
    /**
     * @param mixed $data
     * @return static
     */
    public function setUserData($data)
    {
        $this->options['user_data'] = $data;
        return $this;
    }
    /**
     * @param int $max
     * @return static
     */
    public function setMaxRedirects($max)
    {
        $this->options['max_redirects'] = $max;
        return $this;
    }
    /**
     * @param string $version
     * @return static
     */
    public function setHttpVersion($version)
    {
        $this->options['http_version'] = $version;
        return $this;
    }
    /**
     * @param string $uri
     * @return static
     */
    public function setBaseUri($uri)
    {
        $this->options['base_uri'] = $uri;
        return $this;
    }
    /**
     * @param bool $buffer
     * @return static
     */
    public function buffer($buffer)
    {
        $this->options['buffer'] = $buffer;
        return $this;
    }
    /**
     * @param callable $callback
     * @return static
     */
    public function setOnProgress($callback)
    {
        $this->options['on_progress'] = $callback;
        return $this;
    }
    /**
     * @param mixed[] $hostIps
     * @return static
     */
    public function resolve($hostIps)
    {
        $this->options['resolve'] = $hostIps;
        return $this;
    }
    /**
     * @param string $proxy
     * @return static
     */
    public function setProxy($proxy)
    {
        $this->options['proxy'] = $proxy;
        return $this;
    }
    /**
     * @param string $noProxy
     * @return static
     */
    public function setNoProxy($noProxy)
    {
        $this->options['no_proxy'] = $noProxy;
        return $this;
    }
    /**
     * @param float $timeout
     * @return static
     */
    public function setTimeout($timeout)
    {
        $this->options['timeout'] = $timeout;
        return $this;
    }
    /**
     * @param float $maxDuration
     * @return static
     */
    public function setMaxDuration($maxDuration)
    {
        $this->options['max_duration'] = $maxDuration;
        return $this;
    }
    /**
     * @param string $bindto
     * @return static
     */
    public function bindTo($bindto)
    {
        $this->options['bindto'] = $bindto;
        return $this;
    }
    /**
     * @param bool $verify
     * @return static
     */
    public function verifyPeer($verify)
    {
        $this->options['verify_peer'] = $verify;
        return $this;
    }
    /**
     * @param bool $verify
     * @return static
     */
    public function verifyHost($verify)
    {
        $this->options['verify_host'] = $verify;
        return $this;
    }
    /**
     * @param string $cafile
     * @return static
     */
    public function setCaFile($cafile)
    {
        $this->options['cafile'] = $cafile;
        return $this;
    }
    /**
     * @param string $capath
     * @return static
     */
    public function setCaPath($capath)
    {
        $this->options['capath'] = $capath;
        return $this;
    }
    /**
     * @param string $cert
     * @return static
     */
    public function setLocalCert($cert)
    {
        $this->options['local_cert'] = $cert;
        return $this;
    }
    /**
     * @param string $pk
     * @return static
     */
    public function setLocalPk($pk)
    {
        $this->options['local_pk'] = $pk;
        return $this;
    }
    /**
     * @param string $passphrase
     * @return static
     */
    public function setPassphrase($passphrase)
    {
        $this->options['passphrase'] = $passphrase;
        return $this;
    }
    /**
     * @param string $ciphers
     * @return static
     */
    public function setCiphers($ciphers)
    {
        $this->options['ciphers'] = $ciphers;
        return $this;
    }
    /**
     * @param string|mixed[] $fingerprint
     * @return static
     */
    public function setPeerFingerprint($fingerprint)
    {
        $this->options['peer_fingerprint'] = $fingerprint;
        return $this;
    }
    /**
     * @param bool $capture
     * @return static
     */
    public function capturePeerCertChain($capture)
    {
        $this->options['capture_peer_cert_chain'] = $capture;
        return $this;
    }
    /**
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function setExtra($name, $value)
    {
        $this->options['extra'][$name] = $value;
        return $this;
    }
}
