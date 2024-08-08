<?php

namespace Staatic\Framework\ResourceRepository;

use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use RuntimeException;
use Staatic\Framework\Resource;
final class InMemoryResourceRepository implements ResourceRepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var mixed[]
     */
    private $resources = [];
    public function __construct()
    {
        $this->logger = new NullLogger();
    }
    /**
     * @param Resource $resource
     */
    public function write($resource): void
    {
        $this->logger->debug("Writing resource with sha1 {$resource->sha1()}");
        $this->resources[$resource->sha1()] = $resource;
    }
    /**
     * @param string $sha1
     */
    public function find($sha1): ?Resource
    {
        return $this->resources[$sha1] ?? null;
    }
    /**
     * @param string $sha1
     */
    public function delete($sha1): void
    {
        if (!isset($this->resources[$sha1])) {
            throw new RuntimeException("Unable to find resource with sha1 {$sha1}");
        }
        unset($this->resources[$sha1]);
    }
}
