<?php

namespace Staatic\Crawler\UrlExtractor;

final class RssUrlExtractor extends AbstractPatternUrlExtractor
{
    protected function getPatterns(): array
    {
        return ['~<link(?:[^>]*)>\s*([^<]+)\s*</link>~', '~<comments(?:[^>]*)>\s*([^<]+)\s*</comments>~', '~<atom:link(?:.+?)href\s*=\s*"\s*([^"]+)\s*"~'];
    }
}
