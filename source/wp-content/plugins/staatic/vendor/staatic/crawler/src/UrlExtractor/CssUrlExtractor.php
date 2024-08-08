<?php

namespace Staatic\Crawler\UrlExtractor;

final class CssUrlExtractor extends AbstractPatternUrlExtractor
{
    protected function getPatterns(): array
    {
        return ['~url\([\s"\']*([^)"\']+?)[\s"\']*\)~', '~@import\s+["\'](\s*[^"\']+\s*)~'];
    }
}
