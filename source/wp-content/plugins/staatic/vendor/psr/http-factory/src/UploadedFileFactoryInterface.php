<?php

namespace Staatic\Vendor\Psr\Http\Message;

interface UploadedFileFactoryInterface
{
    /**
     * @param StreamInterface $stream
     * @param int|null $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     */
    public function createUploadedFile($stream, $size = null, $error = \UPLOAD_ERR_OK, $clientFilename = null, $clientMediaType = null): UploadedFileInterface;
}
