<?php

namespace Staatic\Crawler\CrawlUrlProvider;

use Generator;
use Staatic\Crawler\CrawlUrl;
interface CrawlUrlProviderInterface
{
    public function provide(): Generator;
}
