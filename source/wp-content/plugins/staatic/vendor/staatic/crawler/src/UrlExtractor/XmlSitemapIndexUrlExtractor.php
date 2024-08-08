<?php

namespace Staatic\Crawler\UrlExtractor;

final class XmlSitemapIndexUrlExtractor extends AbstractPatternUrlExtractor
{
    protected function getPatterns(): array
    {
        return ['~<loc(?:[^>]*)>\s*([^<]+?)\s*</loc>~'];
    }
}
