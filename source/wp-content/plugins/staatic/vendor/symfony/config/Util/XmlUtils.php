<?php

namespace Staatic\Vendor\Symfony\Component\Config\Util;

use DOMDocument;
use LogicException;
use Exception;
use InvalidArgumentException;
use DOMText;
use DOMComment;
use DOMElement;
use Stringable;
use Staatic\Vendor\Symfony\Component\Config\Util\Exception\InvalidXmlException;
use Staatic\Vendor\Symfony\Component\Config\Util\Exception\XmlParsingException;
class XmlUtils
{
    private function __construct()
    {
    }
    /**
     * @param string $content
     * @param string|callable|null $schemaOrCallable
     */
    public static function parse($content, $schemaOrCallable = null): DOMDocument
    {
        if (!\extension_loaded('dom')) {
            throw new LogicException('Extension DOM is required.');
        }
        $internalErrors = libxml_use_internal_errors(\true);
        libxml_clear_errors();
        $dom = new DOMDocument();
        $dom->validateOnParse = \true;
        if (!$dom->loadXML($content, \LIBXML_NONET | \LIBXML_COMPACT)) {
            throw new XmlParsingException(implode("\n", static::getXmlErrors($internalErrors)));
        }
        $dom->normalizeDocument();
        libxml_use_internal_errors($internalErrors);
        foreach ($dom->childNodes as $child) {
            if (\XML_DOCUMENT_TYPE_NODE === $child->nodeType) {
                throw new XmlParsingException('Document types are not allowed.');
            }
        }
        if (null !== $schemaOrCallable) {
            $internalErrors = libxml_use_internal_errors(\true);
            libxml_clear_errors();
            $e = null;
            if (\is_callable($schemaOrCallable)) {
                try {
                    $valid = $schemaOrCallable($dom, $internalErrors);
                } catch (Exception $e) {
                    $valid = \false;
                }
            } elseif (is_file($schemaOrCallable)) {
                $schemaSource = file_get_contents((string) $schemaOrCallable);
                $valid = @$dom->schemaValidateSource($schemaSource);
            } else {
                libxml_use_internal_errors($internalErrors);
                throw new XmlParsingException(sprintf('Invalid XSD file: "%s".', $schemaOrCallable));
            }
            if (!$valid) {
                $messages = static::getXmlErrors($internalErrors);
                if (!$messages) {
                    throw new InvalidXmlException('The XML is not valid.', 0, $e);
                }
                throw new XmlParsingException(implode("\n", $messages), 0, $e);
            }
        }
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
        return $dom;
    }
    /**
     * @param string $file
     * @param string|callable|null $schemaOrCallable
     */
    public static function loadFile($file, $schemaOrCallable = null): DOMDocument
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException(sprintf('Resource "%s" is not a file.', $file));
        }
        if (!is_readable($file)) {
            throw new InvalidArgumentException(sprintf('File "%s" is not readable.', $file));
        }
        $content = @file_get_contents($file);
        if ('' === trim($content)) {
            throw new InvalidArgumentException(sprintf('File "%s" does not contain valid XML, it is empty.', $file));
        }
        try {
            return static::parse($content, $schemaOrCallable);
        } catch (InvalidXmlException $e) {
            throw new XmlParsingException(sprintf('The XML file "%s" is not valid.', $file), 0, $e->getPrevious());
        }
    }
    /**
     * @param DOMElement $element
     * @param bool $checkPrefix
     * @return mixed
     */
    public static function convertDomElementToArray($element, $checkPrefix = \true)
    {
        $prefix = (string) $element->prefix;
        $empty = \true;
        $config = [];
        foreach ($element->attributes as $name => $node) {
            if ($checkPrefix && !\in_array((string) $node->prefix, ['', $prefix], \true)) {
                continue;
            }
            $config[$name] = static::phpize($node->value);
            $empty = \false;
        }
        $nodeValue = \false;
        foreach ($element->childNodes as $node) {
            if ($node instanceof DOMText) {
                if ('' !== trim($node->nodeValue)) {
                    $nodeValue = trim($node->nodeValue);
                    $empty = \false;
                }
            } elseif ($checkPrefix && $prefix != (string) $node->prefix) {
                continue;
            } elseif (!$node instanceof DOMComment) {
                $value = static::convertDomElementToArray($node, $checkPrefix);
                $key = $node->localName;
                if (isset($config[$key])) {
                    if (!\is_array($config[$key]) || !\is_int(key($config[$key]))) {
                        $config[$key] = [$config[$key]];
                    }
                    $config[$key][] = $value;
                } else {
                    $config[$key] = $value;
                }
                $empty = \false;
            }
        }
        if (\false !== $nodeValue) {
            $value = static::phpize($nodeValue);
            if (\count($config)) {
                $config['value'] = $value;
            } else {
                $config = $value;
            }
        }
        return (!$empty) ? $config : null;
    }
    /**
     * @param string|Stringable $value
     * @return mixed
     */
    public static function phpize($value)
    {
        $value = (string) $value;
        $lowercaseValue = strtolower($value);
        switch (\true) {
            case 'null' === $lowercaseValue:
                return null;
            case ctype_digit($value):
            case isset($value[1]) && '-' === $value[0] && ctype_digit(substr($value, 1)):
                $raw = $value;
                $cast = (int) $value;
                return self::isOctal($value) ? \intval($value, 8) : (($raw === (string) $cast) ? $cast : $raw);
            case 'true' === $lowercaseValue:
                return \true;
            case 'false' === $lowercaseValue:
                return \false;
            case isset($value[1]) && '0b' == $value[0] . $value[1] && preg_match('/^0b[01]*$/', $value):
                return bindec($value);
            case is_numeric($value):
                return ('0x' === $value[0] . $value[1]) ? hexdec($value) : (float) $value;
            case preg_match('/^0x[0-9a-f]++$/i', $value):
                return hexdec($value);
            case preg_match('/^[+-]?[0-9]+(\.[0-9]+)?$/', $value):
                return (float) $value;
            default:
                return $value;
        }
    }
    /**
     * @param bool $internalErrors
     */
    protected static function getXmlErrors($internalErrors)
    {
        $errors = [];
        foreach (libxml_get_errors() as $error) {
            $errors[] = sprintf('[%s %s] %s (in %s - line %d, column %d)', (\LIBXML_ERR_WARNING == $error->level) ? 'WARNING' : 'ERROR', $error->code, trim($error->message), $error->file ?: 'n/a', $error->line, $error->column);
        }
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
        return $errors;
    }
    private static function isOctal(string $str): bool
    {
        if ('-' === $str[0]) {
            $str = substr($str, 1);
        }
        return $str === '0' . decoct(\intval($str, 8));
    }
}
