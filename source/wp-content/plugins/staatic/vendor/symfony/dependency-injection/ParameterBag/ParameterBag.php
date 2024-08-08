<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag;

use Stringable;
use UnitEnum;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
class ParameterBag implements ParameterBagInterface
{
    protected $parameters = [];
    protected $resolved = \false;
    public function __construct(array $parameters = [])
    {
        $this->add($parameters);
    }
    public function clear()
    {
        $this->parameters = [];
    }
    /**
     * @param mixed[] $parameters
     */
    public function add($parameters)
    {
        foreach ($parameters as $key => $value) {
            $this->set($key, $value);
        }
    }
    public function all(): array
    {
        return $this->parameters;
    }
    /**
     * @param string $name
     * @return mixed[]|bool|string|int|float|UnitEnum|null
     */
    public function get($name)
    {
        if (!\array_key_exists($name, $this->parameters)) {
            if (!$name) {
                throw new ParameterNotFoundException($name);
            }
            $alternatives = [];
            foreach ($this->parameters as $key => $parameterValue) {
                $lev = levenshtein($name, $key);
                if ($lev <= \strlen($name) / 3 || strpos($key, $name) !== false) {
                    $alternatives[] = $key;
                }
            }
            $nonNestedAlternative = null;
            if (!\count($alternatives) && strpos($name, '.') !== false) {
                $namePartsLength = array_map('strlen', explode('.', $name));
                $key = substr($name, 0, -1 * (1 + array_pop($namePartsLength)));
                while (\count($namePartsLength)) {
                    if ($this->has($key)) {
                        if (\is_array($this->get($key))) {
                            $nonNestedAlternative = $key;
                        }
                        break;
                    }
                    $key = substr($key, 0, -1 * (1 + array_pop($namePartsLength)));
                }
            }
            throw new ParameterNotFoundException($name, null, null, null, $alternatives, $nonNestedAlternative);
        }
        return $this->parameters[$name];
    }
    /**
     * @param string $name
     * @param mixed[]|bool|string|int|float|UnitEnum|null $value
     */
    public function set($name, $value)
    {
        if (is_numeric($name)) {
            trigger_deprecation('symfony/dependency-injection', '6.2', sprintf('Using numeric parameter name "%s" is deprecated and will throw as of 7.0.', $name));
        }
        $this->parameters[$name] = $value;
    }
    /**
     * @param string $name
     */
    public function has($name): bool
    {
        return \array_key_exists($name, $this->parameters);
    }
    /**
     * @param string $name
     */
    public function remove($name)
    {
        unset($this->parameters[$name]);
    }
    public function resolve()
    {
        if ($this->resolved) {
            return;
        }
        $parameters = [];
        foreach ($this->parameters as $key => $value) {
            try {
                $value = $this->resolveValue($value);
                $parameters[$key] = $this->unescapeValue($value);
            } catch (ParameterNotFoundException $e) {
                $e->setSourceKey($key);
                throw $e;
            }
        }
        $this->parameters = $parameters;
        $this->resolved = \true;
    }
    /**
     * @param mixed $value
     * @param mixed[] $resolving
     * @return mixed
     */
    public function resolveValue($value, $resolving = [])
    {
        if (\is_array($value)) {
            $args = [];
            foreach ($value as $key => $v) {
                $resolvedKey = \is_string($key) ? $this->resolveValue($key, $resolving) : $key;
                if (!\is_scalar($resolvedKey) && !$resolvedKey instanceof Stringable) {
                    throw new RuntimeException(sprintf('Array keys must be a scalar-value, but found key "%s" to resolve to type "%s".', $key, get_debug_type($resolvedKey)));
                }
                $args[$resolvedKey] = $this->resolveValue($v, $resolving);
            }
            return $args;
        }
        if (!\is_string($value) || 2 > \strlen($value)) {
            return $value;
        }
        return $this->resolveString($value, $resolving);
    }
    /**
     * @param string $value
     * @param mixed[] $resolving
     * @return mixed
     */
    public function resolveString($value, $resolving = [])
    {
        if (preg_match('/^%([^%\s]+)%$/', $value, $match)) {
            $key = $match[1];
            if (isset($resolving[$key])) {
                throw new ParameterCircularReferenceException(array_keys($resolving));
            }
            $resolving[$key] = \true;
            return $this->resolved ? $this->get($key) : $this->resolveValue($this->get($key), $resolving);
        }
        return preg_replace_callback('/%%|%([^%\s]+)%/', function ($match) use ($resolving, $value) {
            if (!isset($match[1])) {
                return '%%';
            }
            $key = $match[1];
            if (isset($resolving[$key])) {
                throw new ParameterCircularReferenceException(array_keys($resolving));
            }
            $resolved = $this->get($key);
            if (!\is_string($resolved) && !is_numeric($resolved)) {
                throw new RuntimeException(sprintf('A string value must be composed of strings and/or numbers, but found parameter "%s" of type "%s" inside string value "%s".', $key, get_debug_type($resolved), $value));
            }
            $resolved = (string) $resolved;
            $resolving[$key] = \true;
            return $this->isResolved() ? $resolved : $this->resolveString($resolved, $resolving);
        }, $value);
    }
    public function isResolved()
    {
        return $this->resolved;
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    public function escapeValue($value)
    {
        if (\is_string($value)) {
            return str_replace('%', '%%', $value);
        }
        if (\is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $result[$k] = $this->escapeValue($v);
            }
            return $result;
        }
        return $value;
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    public function unescapeValue($value)
    {
        if (\is_string($value)) {
            return str_replace('%%', '%', $value);
        }
        if (\is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $result[$k] = $this->unescapeValue($v);
            }
            return $result;
        }
        return $value;
    }
}
