<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\Crawler\CrawlOptions;
use Staatic\WordPress\Setting\AbstractSetting;

final class CrawlerDomParserSetting extends AbstractSetting
{
    public const VALUE_HTML5 = CrawlOptions::DOM_PARSER_HTML5;

    public const VALUE_DOM_WRAP = CrawlOptions::DOM_PARSER_DOM_WRAP;

    public const VALUE_SIMPLE_HTML = CrawlOptions::DOM_PARSER_SIMPLE_HTML;

    public function name(): string
    {
        return 'staatic_crawler_dom_parser';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    protected function template(): string
    {
        return 'select';
    }

    public function label(): string
    {
        return __('HTML DOM parser', 'staatic');
    }

    public function description(): ?string
    {
        return __('This determines which HTML DOM parser is used while transforming your site.<br>If you are experiencing issues with the generated static HTML, you can try a different parser.', 'staatic');
    }

    public function defaultValue()
    {
        return self::VALUE_DOM_WRAP;
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        parent::render(array_merge([
            'selectOptions' => $this->selectOptions()
        ], $attributes));
    }

    private function selectOptions(): array
    {
        return [
            self::VALUE_HTML5 => 'HTML5-PHP (masterminds/html5)',
            self::VALUE_DOM_WRAP => 'PHP DOM Wrapper (scotteh/php-dom-wrapper)',
            self::VALUE_SIMPLE_HTML => 'Simple Html Dom Parser (voku/simple_html_dom)'
        ];
    }
}
