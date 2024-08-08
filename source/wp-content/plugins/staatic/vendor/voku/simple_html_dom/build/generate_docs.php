<?php

namespace Staatic\Vendor;

use Staatic\Vendor\voku\PhpReadmeHelper\GenerateApi;
use Staatic\Vendor\voku\helper\DomParserInterface;
use Staatic\Vendor\voku\helper\SimpleHtmlDomNodeInterface;
use Staatic\Vendor\voku\helper\SimpleHtmlDomInterface;
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';
$readmeText = (new GenerateApi())->generate(__DIR__ . '/../src/', __DIR__ . '/docs/api.md', [DomParserInterface::class, SimpleHtmlDomNodeInterface::class, SimpleHtmlDomInterface::class]);
\file_put_contents(__DIR__ . '/../README_API.md', $readmeText);
