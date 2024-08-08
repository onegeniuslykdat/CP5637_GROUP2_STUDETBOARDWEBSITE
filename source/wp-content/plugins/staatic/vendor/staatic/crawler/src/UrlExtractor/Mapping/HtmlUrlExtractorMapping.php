<?php

namespace Staatic\Crawler\UrlExtractor\Mapping;

class HtmlUrlExtractorMapping
{
    /**
     * @var mixed[]
     */
    protected $mapping;
    /**
     * @var mixed[]
     */
    protected $styleAttributes;
    /**
     * @var mixed[]
     */
    protected $srcsetAttributes;
    public function __construct()
    {
        $this->mapping = ['a' => ['href', 'urn'], 'base' => ['href'], 'form' => ['action', 'data'], 'img' => ['src', 'usemap', 'longdesc', 'dynsrc', 'lowsrc', 'srcset'], 'amp-img' => ['src', 'srcset'], 'link' => ['href'], 'applet' => ['code', 'codebase', 'archive', 'object'], 'area' => ['href'], 'body' => ['background', 'credits', 'instructions', 'logo'], 'input' => ['src', 'usemap', 'dynsrc', 'lowsrc', 'action', 'formaction'], 'blockquote' => ['cite'], 'del' => ['cite'], 'frame' => ['longdesc', 'src'], 'head' => ['profile'], 'iframe' => ['longdesc', 'src'], 'ins' => ['cite'], 'object' => ['archive', 'classid', 'codebase', 'data', 'usemap'], 'q' => ['cite'], 'script' => ['src'], 'audio' => ['src'], 'command' => ['icon'], 'embed' => ['src', 'code', 'pluginspage'], 'event-source' => ['src'], 'html' => ['manifest', 'background', 'xmlns'], 'source' => ['src', 'srcset'], 'video' => ['src', 'poster'], 'bgsound' => ['src'], 'div' => ['href', 'src'], 'ilayer' => ['src'], 'table' => ['background'], 'td' => ['background'], 'th' => ['background'], 'layer' => ['src'], 'xml' => ['src'], 'button' => ['action', 'formaction'], 'datalist' => ['data'], 'select' => ['data'], 'access' => ['path'], 'card' => ['onenterforward', 'onenterbackward', 'ontimer'], 'go' => ['href'], 'option' => ['onpick'], 'template' => ['onenterforward', 'onenterbackward', 'ontimer'], 'wml' => ['xmlns']];
        $this->styleAttributes = ['style'];
        $this->srcsetAttributes = ['srcset'];
    }
    public function mapping(): array
    {
        return $this->mapping;
    }
    public function styleAttributes(): array
    {
        return $this->styleAttributes;
    }
    public function srcsetAttributes(): array
    {
        return $this->srcsetAttributes;
    }
}
