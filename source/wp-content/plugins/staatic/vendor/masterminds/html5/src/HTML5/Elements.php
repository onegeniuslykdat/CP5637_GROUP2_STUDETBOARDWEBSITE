<?php

namespace Staatic\Vendor\Masterminds\HTML5;

class Elements
{
    const KNOWN_ELEMENT = 1;
    const TEXT_RAW = 2;
    const TEXT_RCDATA = 4;
    const VOID_TAG = 8;
    const AUTOCLOSE_P = 16;
    const TEXT_PLAINTEXT = 32;
    const BLOCK_TAG = 64;
    const BLOCK_ONLY_INLINE = 128;
    public static $optionalEndElementsParentsToClose = array('tr' => array('td', 'tr'), 'td' => array('td', 'th'), 'th' => array('td', 'th'), 'tfoot' => array('td', 'th', 'tr', 'tbody', 'thead'), 'tbody' => array('td', 'th', 'tr', 'thead'));
    public static $html5 = array('a' => 1, 'abbr' => 1, 'address' => 65, 'area' => 9, 'article' => 81, 'aside' => 81, 'audio' => 1, 'b' => 1, 'base' => 9, 'bdi' => 1, 'bdo' => 1, 'blockquote' => 81, 'body' => 1, 'br' => 9, 'button' => 1, 'canvas' => 65, 'caption' => 1, 'cite' => 1, 'code' => 1, 'col' => 9, 'colgroup' => 1, 'command' => 9, 'datalist' => 1, 'dd' => 65, 'del' => 1, 'details' => 17, 'dfn' => 1, 'dialog' => 17, 'div' => 81, 'dl' => 81, 'dt' => 1, 'em' => 1, 'embed' => 9, 'fieldset' => 81, 'figcaption' => 81, 'figure' => 81, 'footer' => 81, 'form' => 81, 'h1' => 81, 'h2' => 81, 'h3' => 81, 'h4' => 81, 'h5' => 81, 'h6' => 81, 'head' => 1, 'header' => 81, 'hgroup' => 81, 'hr' => 73, 'html' => 1, 'i' => 1, 'iframe' => 3, 'img' => 9, 'input' => 9, 'kbd' => 1, 'ins' => 1, 'keygen' => 9, 'label' => 1, 'legend' => 1, 'li' => 1, 'link' => 9, 'map' => 1, 'mark' => 1, 'menu' => 17, 'meta' => 9, 'meter' => 1, 'nav' => 17, 'noscript' => 65, 'object' => 1, 'ol' => 81, 'optgroup' => 1, 'option' => 1, 'output' => 65, 'p' => 209, 'param' => 9, 'pre' => 81, 'progress' => 1, 'q' => 1, 'rp' => 1, 'rt' => 1, 'ruby' => 1, 's' => 1, 'samp' => 1, 'script' => 3, 'section' => 81, 'select' => 1, 'small' => 1, 'source' => 9, 'span' => 1, 'strong' => 1, 'style' => 3, 'sub' => 1, 'summary' => 17, 'sup' => 1, 'table' => 65, 'tbody' => 1, 'td' => 1, 'textarea' => 5, 'tfoot' => 65, 'th' => 1, 'thead' => 1, 'time' => 1, 'title' => 5, 'tr' => 1, 'track' => 9, 'u' => 1, 'ul' => 81, 'var' => 1, 'video' => 1, 'wbr' => 9, 'basefont' => 8, 'bgsound' => 8, 'noframes' => 2, 'frame' => 9, 'frameset' => 1, 'center' => 16, 'dir' => 16, 'listing' => 16, 'plaintext' => 48, 'applet' => 0, 'marquee' => 0, 'isindex' => 8, 'xmp' => 20, 'noembed' => 2);
    public static $mathml = array('maction' => 1, 'maligngroup' => 1, 'malignmark' => 1, 'math' => 1, 'menclose' => 1, 'merror' => 1, 'mfenced' => 1, 'mfrac' => 1, 'mglyph' => 1, 'mi' => 1, 'mlabeledtr' => 1, 'mlongdiv' => 1, 'mmultiscripts' => 1, 'mn' => 1, 'mo' => 1, 'mover' => 1, 'mpadded' => 1, 'mphantom' => 1, 'mroot' => 1, 'mrow' => 1, 'ms' => 1, 'mscarries' => 1, 'mscarry' => 1, 'msgroup' => 1, 'msline' => 1, 'mspace' => 1, 'msqrt' => 1, 'msrow' => 1, 'mstack' => 1, 'mstyle' => 1, 'msub' => 1, 'msup' => 1, 'msubsup' => 1, 'mtable' => 1, 'mtd' => 1, 'mtext' => 1, 'mtr' => 1, 'munder' => 1, 'munderover' => 1);
    public static $svg = array('a' => 1, 'altGlyph' => 1, 'altGlyphDef' => 1, 'altGlyphItem' => 1, 'animate' => 1, 'animateColor' => 1, 'animateMotion' => 1, 'animateTransform' => 1, 'circle' => 1, 'clipPath' => 1, 'color-profile' => 1, 'cursor' => 1, 'defs' => 1, 'desc' => 1, 'ellipse' => 1, 'feBlend' => 1, 'feColorMatrix' => 1, 'feComponentTransfer' => 1, 'feComposite' => 1, 'feConvolveMatrix' => 1, 'feDiffuseLighting' => 1, 'feDisplacementMap' => 1, 'feDistantLight' => 1, 'feFlood' => 1, 'feFuncA' => 1, 'feFuncB' => 1, 'feFuncG' => 1, 'feFuncR' => 1, 'feGaussianBlur' => 1, 'feImage' => 1, 'feMerge' => 1, 'feMergeNode' => 1, 'feMorphology' => 1, 'feOffset' => 1, 'fePointLight' => 1, 'feSpecularLighting' => 1, 'feSpotLight' => 1, 'feTile' => 1, 'feTurbulence' => 1, 'filter' => 1, 'font' => 1, 'font-face' => 1, 'font-face-format' => 1, 'font-face-name' => 1, 'font-face-src' => 1, 'font-face-uri' => 1, 'foreignObject' => 1, 'g' => 1, 'glyph' => 1, 'glyphRef' => 1, 'hkern' => 1, 'image' => 1, 'line' => 1, 'linearGradient' => 1, 'marker' => 1, 'mask' => 1, 'metadata' => 1, 'missing-glyph' => 1, 'mpath' => 1, 'path' => 1, 'pattern' => 1, 'polygon' => 1, 'polyline' => 1, 'radialGradient' => 1, 'rect' => 1, 'script' => 3, 'set' => 1, 'stop' => 1, 'style' => 3, 'svg' => 1, 'switch' => 1, 'symbol' => 1, 'text' => 1, 'textPath' => 1, 'title' => 1, 'tref' => 1, 'tspan' => 1, 'use' => 1, 'view' => 1, 'vkern' => 1);
    public static $svgCaseSensitiveAttributeMap = array('attributename' => 'attributeName', 'attributetype' => 'attributeType', 'basefrequency' => 'baseFrequency', 'baseprofile' => 'baseProfile', 'calcmode' => 'calcMode', 'clippathunits' => 'clipPathUnits', 'contentscripttype' => 'contentScriptType', 'contentstyletype' => 'contentStyleType', 'diffuseconstant' => 'diffuseConstant', 'edgemode' => 'edgeMode', 'externalresourcesrequired' => 'externalResourcesRequired', 'filterres' => 'filterRes', 'filterunits' => 'filterUnits', 'glyphref' => 'glyphRef', 'gradienttransform' => 'gradientTransform', 'gradientunits' => 'gradientUnits', 'kernelmatrix' => 'kernelMatrix', 'kernelunitlength' => 'kernelUnitLength', 'keypoints' => 'keyPoints', 'keysplines' => 'keySplines', 'keytimes' => 'keyTimes', 'lengthadjust' => 'lengthAdjust', 'limitingconeangle' => 'limitingConeAngle', 'markerheight' => 'markerHeight', 'markerunits' => 'markerUnits', 'markerwidth' => 'markerWidth', 'maskcontentunits' => 'maskContentUnits', 'maskunits' => 'maskUnits', 'numoctaves' => 'numOctaves', 'pathlength' => 'pathLength', 'patterncontentunits' => 'patternContentUnits', 'patterntransform' => 'patternTransform', 'patternunits' => 'patternUnits', 'pointsatx' => 'pointsAtX', 'pointsaty' => 'pointsAtY', 'pointsatz' => 'pointsAtZ', 'preservealpha' => 'preserveAlpha', 'preserveaspectratio' => 'preserveAspectRatio', 'primitiveunits' => 'primitiveUnits', 'refx' => 'refX', 'refy' => 'refY', 'repeatcount' => 'repeatCount', 'repeatdur' => 'repeatDur', 'requiredextensions' => 'requiredExtensions', 'requiredfeatures' => 'requiredFeatures', 'specularconstant' => 'specularConstant', 'specularexponent' => 'specularExponent', 'spreadmethod' => 'spreadMethod', 'startoffset' => 'startOffset', 'stddeviation' => 'stdDeviation', 'stitchtiles' => 'stitchTiles', 'surfacescale' => 'surfaceScale', 'systemlanguage' => 'systemLanguage', 'tablevalues' => 'tableValues', 'targetx' => 'targetX', 'targety' => 'targetY', 'textlength' => 'textLength', 'viewbox' => 'viewBox', 'viewtarget' => 'viewTarget', 'xchannelselector' => 'xChannelSelector', 'ychannelselector' => 'yChannelSelector', 'zoomandpan' => 'zoomAndPan');
    public static $svgCaseSensitiveElementMap = array('altglyph' => 'altGlyph', 'altglyphdef' => 'altGlyphDef', 'altglyphitem' => 'altGlyphItem', 'animatecolor' => 'animateColor', 'animatemotion' => 'animateMotion', 'animatetransform' => 'animateTransform', 'clippath' => 'clipPath', 'feblend' => 'feBlend', 'fecolormatrix' => 'feColorMatrix', 'fecomponenttransfer' => 'feComponentTransfer', 'fecomposite' => 'feComposite', 'feconvolvematrix' => 'feConvolveMatrix', 'fediffuselighting' => 'feDiffuseLighting', 'fedisplacementmap' => 'feDisplacementMap', 'fedistantlight' => 'feDistantLight', 'feflood' => 'feFlood', 'fefunca' => 'feFuncA', 'fefuncb' => 'feFuncB', 'fefuncg' => 'feFuncG', 'fefuncr' => 'feFuncR', 'fegaussianblur' => 'feGaussianBlur', 'feimage' => 'feImage', 'femerge' => 'feMerge', 'femergenode' => 'feMergeNode', 'femorphology' => 'feMorphology', 'feoffset' => 'feOffset', 'fepointlight' => 'fePointLight', 'fespecularlighting' => 'feSpecularLighting', 'fespotlight' => 'feSpotLight', 'fetile' => 'feTile', 'feturbulence' => 'feTurbulence', 'foreignobject' => 'foreignObject', 'glyphref' => 'glyphRef', 'lineargradient' => 'linearGradient', 'radialgradient' => 'radialGradient', 'textpath' => 'textPath');
    public static function isA($name, $mask)
    {
        return (static::element($name) & $mask) === $mask;
    }
    public static function isHtml5Element($name)
    {
        return isset(static::$html5[strtolower($name)]);
    }
    public static function isMathMLElement($name)
    {
        return isset(static::$mathml[$name]);
    }
    public static function isSvgElement($name)
    {
        return isset(static::$svg[$name]);
    }
    public static function isElement($name)
    {
        return static::isHtml5Element($name) || static::isMathMLElement($name) || static::isSvgElement($name);
    }
    public static function element($name)
    {
        if (isset(static::$html5[$name])) {
            return static::$html5[$name];
        }
        if (isset(static::$svg[$name])) {
            return static::$svg[$name];
        }
        if (isset(static::$mathml[$name])) {
            return static::$mathml[$name];
        }
        return 0;
    }
    public static function normalizeSvgElement($name)
    {
        $name = strtolower($name);
        if (isset(static::$svgCaseSensitiveElementMap[$name])) {
            $name = static::$svgCaseSensitiveElementMap[$name];
        }
        return $name;
    }
    public static function normalizeSvgAttribute($name)
    {
        $name = strtolower($name);
        if (isset(static::$svgCaseSensitiveAttributeMap[$name])) {
            $name = static::$svgCaseSensitiveAttributeMap[$name];
        }
        return $name;
    }
    public static function normalizeMathMlAttribute($name)
    {
        $name = strtolower($name);
        if ('definitionurl' === $name) {
            $name = 'definitionURL';
        }
        return $name;
    }
}
