<?php

namespace Staatic\Vendor\Masterminds;

use Staatic\Vendor\Masterminds\HTML5\Parser\DOMTreeBuilder;
use Staatic\Vendor\Masterminds\HTML5\Parser\Scanner;
use Staatic\Vendor\Masterminds\HTML5\Parser\Tokenizer;
use Staatic\Vendor\Masterminds\HTML5\Serializer\OutputRules;
use Staatic\Vendor\Masterminds\HTML5\Serializer\Traverser;
class HTML5
{
    private $defaultOptions = array('encode_entities' => \false, 'disable_html_ns' => \false);
    protected $errors = array();
    public function __construct(array $defaultOptions = array())
    {
        $this->defaultOptions = array_merge($this->defaultOptions, $defaultOptions);
    }
    public function getOptions()
    {
        return $this->defaultOptions;
    }
    /**
     * @param mixed[] $options
     */
    public function load($file, $options = array())
    {
        if (is_resource($file)) {
            return $this->parse(stream_get_contents($file), $options);
        }
        return $this->parse(file_get_contents($file), $options);
    }
    /**
     * @param mixed[] $options
     */
    public function loadHTML($string, $options = array())
    {
        return $this->parse($string, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function loadHTMLFile($file, $options = array())
    {
        return $this->load($file, $options);
    }
    /**
     * @param mixed[] $options
     */
    public function loadHTMLFragment($string, $options = array())
    {
        return $this->parseFragment($string, $options);
    }
    public function getErrors()
    {
        return $this->errors;
    }
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }
    /**
     * @param mixed[] $options
     */
    public function parse($input, $options = array())
    {
        $this->errors = array();
        $options = array_merge($this->defaultOptions, $options);
        $events = new DOMTreeBuilder(\false, $options);
        $scanner = new Scanner($input, (!empty($options['encoding'])) ? $options['encoding'] : 'UTF-8');
        $parser = new Tokenizer($scanner, $events, (!empty($options['xmlNamespaces'])) ? Tokenizer::CONFORMANT_XML : Tokenizer::CONFORMANT_HTML);
        $parser->parse();
        $this->errors = $events->getErrors();
        return $events->document();
    }
    /**
     * @param mixed[] $options
     */
    public function parseFragment($input, $options = array())
    {
        $options = array_merge($this->defaultOptions, $options);
        $events = new DOMTreeBuilder(\true, $options);
        $scanner = new Scanner($input, (!empty($options['encoding'])) ? $options['encoding'] : 'UTF-8');
        $parser = new Tokenizer($scanner, $events, (!empty($options['xmlNamespaces'])) ? Tokenizer::CONFORMANT_XML : Tokenizer::CONFORMANT_HTML);
        $parser->parse();
        $this->errors = $events->getErrors();
        return $events->fragment();
    }
    public function save($dom, $file, $options = array())
    {
        $close = \true;
        if (is_resource($file)) {
            $stream = $file;
            $close = \false;
        } else {
            $stream = fopen($file, 'wb');
        }
        $options = array_merge($this->defaultOptions, $options);
        $rules = new OutputRules($stream, $options);
        $trav = new Traverser($dom, $stream, $rules, $options);
        $trav->walk();
        $rules->unsetTraverser();
        if ($close) {
            fclose($stream);
        }
    }
    public function saveHTML($dom, $options = array())
    {
        $stream = fopen('php://temp', 'wb');
        $this->save($dom, $stream, array_merge($this->defaultOptions, $options));
        $html = stream_get_contents($stream, -1, 0);
        fclose($stream);
        return $html;
    }
}
