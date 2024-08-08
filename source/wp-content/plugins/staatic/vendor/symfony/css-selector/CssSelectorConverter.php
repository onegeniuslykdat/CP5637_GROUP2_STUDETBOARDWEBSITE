<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector;

use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Shortcut\ClassParser;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Shortcut\ElementParser;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Shortcut\EmptyStringParser;
use Staatic\Vendor\Symfony\Component\CssSelector\Parser\Shortcut\HashParser;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension\HtmlExtension;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Translator;
class CssSelectorConverter
{
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var mixed[]
     */
    private $cache;
    /**
     * @var mixed[]
     */
    private static $xmlCache = [];
    /**
     * @var mixed[]
     */
    private static $htmlCache = [];
    public function __construct(bool $html = \true)
    {
        $this->translator = new Translator();
        if ($html) {
            $this->translator->registerExtension(new HtmlExtension($this->translator));
            $this->cache =& self::$htmlCache;
        } else {
            $this->cache =& self::$xmlCache;
        }
        $this->translator->registerParserShortcut(new EmptyStringParser())->registerParserShortcut(new ElementParser())->registerParserShortcut(new ClassParser())->registerParserShortcut(new HashParser());
    }
    /**
     * @param string $cssExpr
     * @param string $prefix
     */
    public function toXPath($cssExpr, $prefix = 'descendant-or-self::'): string
    {
        return $this->cache[$prefix][$cssExpr] = $this->cache[$prefix][$cssExpr] ?? $this->translator->cssToXPath($cssExpr, $prefix);
    }
}
