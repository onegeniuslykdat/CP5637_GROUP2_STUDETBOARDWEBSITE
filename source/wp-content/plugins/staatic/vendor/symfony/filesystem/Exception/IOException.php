<?php

namespace Staatic\Vendor\Symfony\Component\Filesystem\Exception;

use RuntimeException;
use Throwable;
class IOException extends RuntimeException implements IOExceptionInterface
{
    /**
     * @var string|null
     */
    private $path;
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null, ?string $path = null)
    {
        $this->path = $path;
        parent::__construct($message, $code, $previous);
    }
    public function getPath(): ?string
    {
        return $this->path;
    }
}
