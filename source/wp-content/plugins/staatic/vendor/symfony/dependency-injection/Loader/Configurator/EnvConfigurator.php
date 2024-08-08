<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use Staatic\Vendor\Symfony\Component\Config\Loader\ParamConfigurator;
class EnvConfigurator extends ParamConfigurator
{
    /**
     * @var mixed[]
     */
    private $stack;
    public function __construct(string $name)
    {
        $this->stack = explode(':', $name);
    }
    public function __toString(): string
    {
        return '%env(' . implode(':', $this->stack) . ')%';
    }
    /**
     * @return static
     */
    public function __call(string $name, array $arguments)
    {
        $processor = strtolower(preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], '\1_\2', $name));
        $this->custom($processor, ...$arguments);
        return $this;
    }
    /**
     * @param string $processor
     * @return static
     */
    public function custom($processor, ...$args)
    {
        array_unshift($this->stack, $processor, ...$args);
        return $this;
    }
    /**
     * @return static
     */
    public function base64()
    {
        array_unshift($this->stack, 'base64');
        return $this;
    }
    /**
     * @return static
     */
    public function bool()
    {
        array_unshift($this->stack, 'bool');
        return $this;
    }
    /**
     * @return static
     */
    public function not()
    {
        array_unshift($this->stack, 'not');
        return $this;
    }
    /**
     * @return static
     */
    public function const()
    {
        array_unshift($this->stack, 'const');
        return $this;
    }
    /**
     * @return static
     */
    public function csv()
    {
        array_unshift($this->stack, 'csv');
        return $this;
    }
    /**
     * @return static
     */
    public function file()
    {
        array_unshift($this->stack, 'file');
        return $this;
    }
    /**
     * @return static
     */
    public function float()
    {
        array_unshift($this->stack, 'float');
        return $this;
    }
    /**
     * @return static
     */
    public function int()
    {
        array_unshift($this->stack, 'int');
        return $this;
    }
    /**
     * @return static
     */
    public function json()
    {
        array_unshift($this->stack, 'json');
        return $this;
    }
    /**
     * @param string $key
     * @return static
     */
    public function key($key)
    {
        array_unshift($this->stack, 'key', $key);
        return $this;
    }
    /**
     * @return static
     */
    public function url()
    {
        array_unshift($this->stack, 'url');
        return $this;
    }
    /**
     * @return static
     */
    public function queryString()
    {
        array_unshift($this->stack, 'query_string');
        return $this;
    }
    /**
     * @return static
     */
    public function resolve()
    {
        array_unshift($this->stack, 'resolve');
        return $this;
    }
    /**
     * @param string $fallbackParam
     * @return static
     */
    public function default($fallbackParam)
    {
        array_unshift($this->stack, 'default', $fallbackParam);
        return $this;
    }
    /**
     * @return static
     */
    public function string()
    {
        array_unshift($this->stack, 'string');
        return $this;
    }
    /**
     * @return static
     */
    public function trim()
    {
        array_unshift($this->stack, 'trim');
        return $this;
    }
    /**
     * @return static
     */
    public function require()
    {
        array_unshift($this->stack, 'require');
        return $this;
    }
    /**
     * @param string $backedEnumClassName
     * @return static
     */
    public function enum($backedEnumClassName)
    {
        array_unshift($this->stack, 'enum', $backedEnumClassName);
        return $this;
    }
}
