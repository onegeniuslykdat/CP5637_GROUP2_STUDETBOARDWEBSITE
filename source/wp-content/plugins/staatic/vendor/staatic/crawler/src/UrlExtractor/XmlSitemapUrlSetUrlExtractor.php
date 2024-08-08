<?php

namespace Staatic\Crawler\UrlExtractor;

final class XmlSitemapUrlSetUrlExtractor extends AbstractPatternUrlExtractor
{
    protected function getPatterns(): array
    {
        return ['~<loc(?:[^>]*)>\s*([^<]+?)\s*</loc>~', '~<image:loc(?:[^>]*)>\s*([^<]+?)\s*</image:loc>~'];
    }
}
