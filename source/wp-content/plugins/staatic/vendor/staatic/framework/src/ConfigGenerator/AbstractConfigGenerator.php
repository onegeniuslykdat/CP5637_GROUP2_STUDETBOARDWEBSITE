<?php

namespace Staatic\Framework\ConfigGenerator;

use Staatic\Framework\Result;
abstract class AbstractConfigGenerator implements ConfigGeneratorInterface
{
    private const DEFAULT_MIME_TYPES = ['apng' => ['image/apng'], 'avif' => ['image/avif'], 'bmp' => ['image/bmp', 'image/x-ms-bmp'], 'gif' => ['image/gif'], 'ico' => ['image/x-icon'], 'cur' => ['image/x-icon'], 'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'png' => ['image/png'], 'svg' => ['image/svg+xml'], 'tif' => ['image/tiff'], 'tiff' => ['image/tiff'], 'webp' => ['image/webp'], 'atom' => ['application/atom+xml'], 'rss' => ['application/rss+xml'], 'eot' => ['application/vnd.ms-fontobject'], 'otf' => ['font/otf'], 'ttf' => ['font/ttf'], 'woff' => ['font/woff'], 'woff2' => ['font/woff2'], 'css' => ['text/css'], 'js' => ['application/javascript'], 'txt' => ['text/plain'], 'xml' => ['application/xml', 'text/xml'], 'xsl' => ['application/xslt+xml']];
    /**
     * @param Result $result
     */
    protected function hasNonStandardMimeType($result): bool
    {
        if (!$result->mimeType()) {
            return \false;
        }
        $pathExtension = pathinfo($result->url()->getPath(), \PATHINFO_EXTENSION);
        if ($pathExtension && isset(self::DEFAULT_MIME_TYPES[$pathExtension])) {
            return !in_array($result->mimeType(), self::DEFAULT_MIME_TYPES[$pathExtension]);
        }
        return $result->mimeType() !== 'text/html';
    }
    /**
     * @param Result $result
     */
    protected function hasNonUtf8Charset($result): bool
    {
        if (!$result->charset()) {
            return \false;
        }
        $charset = str_replace('-', '', strtolower($result->charset()));
        return $charset !== 'utf8';
    }
}
