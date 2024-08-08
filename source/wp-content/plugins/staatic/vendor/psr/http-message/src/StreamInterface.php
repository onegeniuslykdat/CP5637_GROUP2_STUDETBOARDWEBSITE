<?php

declare (strict_types=1);
namespace Staatic\Vendor\Psr\Http\Message;

interface StreamInterface
{
    public function __toString();
    public function close();
    public function detach();
    public function getSize();
    public function tell();
    public function eof();
    public function isSeekable();
    /**
     * @param int $offset
     * @param int $whence
     */
    public function seek($offset, $whence = \SEEK_SET);
    public function rewind();
    public function isWritable();
    /**
     * @param string $string
     */
    public function write($string);
    public function isReadable();
    /**
     * @param int $length
     */
    public function read($length);
    public function getContents();
    /**
     * @param string|null $key
     */
    public function getMetadata($key = null);
}
