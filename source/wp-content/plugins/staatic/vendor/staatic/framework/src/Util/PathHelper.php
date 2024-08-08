<?php

namespace Staatic\Framework\Util;

final class PathHelper
{
    public static function determineFilePath(string $uriPath, bool $htmlAsDirectories = \false): string
    {
        $filePath = rawurldecode($uriPath);
        $filePath = preg_replace('~/+~', '/', $filePath);
        $filePath = '/' . ltrim($filePath, '/');
        if (substr_compare($filePath, '/', -strlen('/')) !== 0) {
            $extension = (($pos = strrpos($filePath, '.')) === \false) ? null : substr($filePath, $pos + 1);
            if ($extension !== null && (!$htmlAsDirectories || !in_array($extension, ['htm', 'html'], \true))) {
                return $filePath;
            }
        }
        $filePath = rtrim($filePath, '/') . '/';
        return $filePath . 'index.html';
    }
}
