<?php

namespace Staatic\Framework\ResourceRepository;

use Staatic\Framework\Resource;
interface ResourceRepositoryInterface
{
    /**
     * @param Resource $resource
     */
    public function write($resource): void;
    /**
     * @param string $sha1
     */
    public function find($sha1): ?Resource;
    /**
     * @param string $sha1
     */
    public function delete($sha1): void;
}
