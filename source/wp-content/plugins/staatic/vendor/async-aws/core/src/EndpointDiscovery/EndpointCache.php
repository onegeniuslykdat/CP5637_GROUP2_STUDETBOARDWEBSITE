<?php

namespace Staatic\Vendor\AsyncAws\Core\EndpointDiscovery;

use Staatic\Vendor\AsyncAws\Core\Exception\LogicException;
class EndpointCache
{
    private $endpoints = [];
    private $expired = [];
    /**
     * @param string|null $region
     * @param mixed[] $endpoints
     */
    public function addEndpoints($region, $endpoints): void
    {
        $now = time();
        if (null === $region) {
            $region = '';
        }
        if (!isset($this->endpoints[$region])) {
            $this->endpoints[$region] = [];
        }
        foreach ($endpoints as $endpoint) {
            $this->endpoints[$region][$this->sanitizeEndpoint($endpoint->getAddress())] = $now + $endpoint->getCachePeriodInMinutes() * 60;
        }
        arsort($this->endpoints[$region]);
    }
    /**
     * @param string $endpoint
     */
    public function removeEndpoint($endpoint): void
    {
        $endpoint = $this->sanitizeEndpoint($endpoint);
        foreach ($this->endpoints as &$endpoints) {
            unset($endpoints[$endpoint]);
        }
        unset($endpoints);
        foreach ($this->expired as &$endpoints) {
            unset($endpoints[$endpoint]);
        }
        unset($endpoints);
    }
    /**
     * @param string|null $region
     */
    public function getActiveEndpoint($region): ?string
    {
        if (null === $region) {
            $region = '';
        }
        $now = time();
        foreach ($this->endpoints[$region] ?? [] as $endpoint => $expiresAt) {
            if ($expiresAt < $now) {
                $this->expired[$region] = \array_slice($this->expired[$region] ?? [], -100);
                unset($this->endpoints[$region][$endpoint]);
                $this->expired[$region][$endpoint] = $expiresAt;
                continue;
            }
            return $endpoint;
        }
        return null;
    }
    /**
     * @param string|null $region
     */
    public function getExpiredEndpoint($region): ?string
    {
        if (null === $region) {
            $region = '';
        }
        if (empty($this->expired[$region])) {
            return null;
        }
        end($this->expired[$region]);
        return key($this->expired[$region]);
    }
    private function sanitizeEndpoint(string $address): string
    {
        $parsed = parse_url($address);
        if (isset($parsed['host'])) {
            return rtrim(sprintf('%s://%s/%s', $parsed['scheme'] ?? 'https', $parsed['host'], ltrim($parsed['path'] ?? '/', '/')), '/');
        }
        if (isset($parsed['path'])) {
            $split = explode('/', $parsed['path'], 2);
            $parsed['host'] = $split[0];
            if (isset($split[1])) {
                $parsed['path'] = $split[1];
            } else {
                $parsed['path'] = '';
            }
            return rtrim(sprintf('%s://%s/%s', $parsed['scheme'] ?? 'https', $parsed['host'], ltrim($parsed['path'], '/')), '/');
        }
        throw new LogicException(sprintf('The supplied endpoint "%s" is invalid.', $address));
    }
}
