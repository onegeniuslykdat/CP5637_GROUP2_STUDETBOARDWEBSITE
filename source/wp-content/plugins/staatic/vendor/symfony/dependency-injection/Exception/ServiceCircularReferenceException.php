<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Exception;

use Throwable;
class ServiceCircularReferenceException extends RuntimeException
{
    /**
     * @var string
     */
    private $serviceId;
    /**
     * @var mixed[]
     */
    private $path;
    public function __construct(string $serviceId, array $path, Throwable $previous = null)
    {
        parent::__construct(sprintf('Circular reference detected for service "%s", path: "%s".', $serviceId, implode(' -> ', $path)), 0, $previous);
        $this->serviceId = $serviceId;
        $this->path = $path;
    }
    public function getServiceId()
    {
        return $this->serviceId;
    }
    public function getPath()
    {
        return $this->path;
    }
}
