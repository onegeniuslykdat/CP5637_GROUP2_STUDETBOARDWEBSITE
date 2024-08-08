<?php

namespace Staatic\Vendor\Symfony\Component\Filesystem\Exception;

interface IOExceptionInterface extends ExceptionInterface
{
    public function getPath(): ?string;
}
