<?php

declare(strict_types=1);

namespace Staatic\WordPress\Bridge;

use Staatic\Crawler\UrlExtractor\Mapping\HtmlUrlExtractorMapping as BaseMapping;

final class HtmlUrlExtractorMapping extends BaseMapping
{
    /**
     * @var mixed[]|null
     */
    private $cachedMapping;

    /**
     * @var mixed[]|null
     */
    private $cachedStyleAttributes;

    /**
     * @var mixed[]|null
     */
    private $cachedSrcsetAttributes;

    public function mapping(): array
    {
        if ($this->cachedMapping === null) {
            $mapping = parent::mapping();
            $mapping['img'] = array_merge($mapping['img'], ['data-src', 'data-srcset']);
            $mapping['amp-img'] = array_merge($mapping['amp-img'], ['data-src', 'data-srcset']);
            $mapping['source'] = array_merge($mapping['source'], ['data-src', 'data-srcset']);
            $mapping['script'] = array_merge($mapping['script'], ['data-src']);
            $this->cachedMapping = apply_filters('staatic_html_mapping_tags', $mapping);
        }

        return $this->cachedMapping;
    }

    public function styleAttributes(): array
    {
        if ($this->cachedStyleAttributes === null) {
            $this->cachedStyleAttributes = apply_filters('staatic_html_mapping_style', parent::styleAttributes());
        }

        return $this->cachedStyleAttributes;
    }

    public function srcsetAttributes(): array
    {
        if ($this->cachedSrcsetAttributes === null) {
            $this->cachedSrcsetAttributes = apply_filters(
                'staatic_html_mapping_srcset',
                array_merge(parent::srcsetAttributes(), ['data-srcset'])
            );
        }

        return $this->cachedSrcsetAttributes;
    }
}
