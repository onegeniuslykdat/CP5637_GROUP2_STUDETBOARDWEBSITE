<?php

namespace Staatic\Crawler\UrlExtractor;

final class RobotsTxtUrlExtractor extends AbstractPatternUrlExtractor
{
    protected function getPatterns(): array
    {
        return ['~^Sitemap:\s*(\S+)~im'];
    }
}
