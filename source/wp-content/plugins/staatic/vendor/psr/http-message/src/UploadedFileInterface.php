<?php

declare (strict_types=1);
namespace Staatic\Vendor\Psr\Http\Message;

interface UploadedFileInterface
{
    public function getStream();
    /**
     * @param string $targetPath
     */
    public function moveTo($targetPath);
    public function getSize();
    public function getError();
    public function getClientFilename();
    public function getClientMediaType();
}
