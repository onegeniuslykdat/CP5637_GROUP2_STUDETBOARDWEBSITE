<?php

namespace Staatic\Crawler\UrlExtractor;

final class XmlUrlExtractor extends AbstractPatternUrlExtractor
{
    protected function getPatterns(): array
    {
        return ['~<\?xml-stylesheet(?:.+?)href="\s*([^"]+?)\s*"~'];
    }
}
