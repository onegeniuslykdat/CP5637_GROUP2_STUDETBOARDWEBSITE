<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use RuntimeException;
use InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\UnsetKeyException;
class ArrayNode extends BaseNode implements PrototypeNodeInterface
{
    protected $xmlRemappings = [];
    protected $children = [];
    protected $allowFalse = \false;
    protected $allowNewKeys = \true;
    protected $addIfNotSet = \false;
    protected $performDeepMerging = \true;
    protected $ignoreExtraKeys = \false;
    protected $removeExtraKeys = \true;
    protected $normalizeKeys = \true;
    /**
     * @param bool $normalizeKeys
     */
    public function setNormalizeKeys($normalizeKeys)
    {
        $this->normalizeKeys = $normalizeKeys;
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    protected function preNormalize($value)
    {
        if (!$this->normalizeKeys || !\is_array($value)) {
            return $value;
        }
        $normalized = [];
        foreach ($value as $k => $v) {
            if (strpos($k, '-') !== false && strpos($k, '_') === false && !\array_key_exists($normalizedKey = str_replace('-', '_', $k), $value)) {
                $normalized[$normalizedKey] = $v;
            } else {
                $normalized[$k] = $v;
            }
        }
        return $normalized;
    }
    public function getChildren(): array
    {
        return $this->children;
    }
    /**
     * @param mixed[] $remappings
     */
    public function setXmlRemappings($remappings)
    {
        $this->xmlRemappings = $remappings;
    }
    public function getXmlRemappings(): array
    {
        return $this->xmlRemappings;
    }
    /**
     * @param bool $boolean
     */
    public function setAddIfNotSet($boolean)
    {
        $this->addIfNotSet = $boolean;
    }
    /**
     * @param bool $allow
     */
    public function setAllowFalse($allow)
    {
        $this->allowFalse = $allow;
    }
    /**
     * @param bool $allow
     */
    public function setAllowNewKeys($allow)
    {
        $this->allowNewKeys = $allow;
    }
    /**
     * @param bool $boolean
     */
    public function setPerformDeepMerging($boolean)
    {
        $this->performDeepMerging = $boolean;
    }
    /**
     * @param bool $boolean
     * @param bool $remove
     */
    public function setIgnoreExtraKeys($boolean, $remove = \true)
    {
        $this->ignoreExtraKeys = $boolean;
        $this->removeExtraKeys = $this->ignoreExtraKeys && $remove;
    }
    public function shouldIgnoreExtraKeys(): bool
    {
        return $this->ignoreExtraKeys;
    }
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    public function hasDefaultValue(): bool
    {
        return $this->addIfNotSet;
    }
    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        if (!$this->hasDefaultValue()) {
            throw new RuntimeException(sprintf('The node at path "%s" has no default value.', $this->getPath()));
        }
        $defaults = [];
        foreach ($this->children as $name => $child) {
            if ($child->hasDefaultValue()) {
                $defaults[$name] = $child->getDefaultValue();
            }
        }
        return $defaults;
    }
    /**
     * @param NodeInterface $node
     */
    public function addChild($node)
    {
        $name = $node->getName();
        if ('' === $name) {
            throw new InvalidArgumentException('Child nodes must be named.');
        }
        if (isset($this->children[$name])) {
            throw new InvalidArgumentException(sprintf('A child node named "%s" already exists.', $name));
        }
        $this->children[$name] = $node;
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    protected function finalizeValue($value)
    {
        if (\false === $value) {
            throw new UnsetKeyException(sprintf('Unsetting key for path "%s", value: %s.', $this->getPath(), json_encode($value)));
        }
        foreach ($this->children as $name => $child) {
            if (!\array_key_exists($name, $value)) {
                if ($child->isRequired()) {
                    $message = sprintf('The child config "%s" under "%s" must be configured', $name, $this->getPath());
                    if ($child->getInfo()) {
                        $message .= sprintf(': %s', $child->getInfo());
                    } else {
                        $message .= '.';
                    }
                    $ex = new InvalidConfigurationException($message);
                    $ex->setPath($this->getPath());
                    throw $ex;
                }
                if ($child->hasDefaultValue()) {
                    $value[$name] = $child->getDefaultValue();
                }
                continue;
            }
            if ($child->isDeprecated()) {
                $deprecation = $child->getDeprecation($name, $this->getPath());
                trigger_deprecation($deprecation['package'], $deprecation['version'], $deprecation['message']);
            }
            try {
                $value[$name] = $child->finalize($value[$name]);
            } catch (UnsetKeyException $exception) {
                unset($value[$name]);
            }
        }
        return $value;
    }
    /**
     * @param mixed $value
     */
    protected function validateType($value)
    {
        if (!\is_array($value) && (!$this->allowFalse || \false !== $value)) {
            $ex = new InvalidTypeException(sprintf('Invalid type for path "%s". Expected "array", but got "%s"', $this->getPath(), get_debug_type($value)));
            if ($hint = $this->getInfo()) {
                $ex->addHint($hint);
            }
            $ex->setPath($this->getPath());
            throw $ex;
        }
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    protected function normalizeValue($value)
    {
        if (\false === $value) {
            return $value;
        }
        $value = $this->remapXml($value);
        $normalized = [];
        foreach ($value as $name => $val) {
            if (isset($this->children[$name])) {
                try {
                    $normalized[$name] = $this->children[$name]->normalize($val);
                } catch (UnsetKeyException $exception) {
                }
                unset($value[$name]);
            } elseif (!$this->removeExtraKeys) {
                $normalized[$name] = $val;
            }
        }
        if (\count($value) && !$this->ignoreExtraKeys) {
            $proposals = array_keys($this->children);
            sort($proposals);
            $guesses = [];
            foreach (array_keys($value) as $subject) {
                $minScore = \INF;
                foreach ($proposals as $proposal) {
                    $distance = levenshtein($subject, $proposal);
                    if ($distance <= $minScore && $distance < 3) {
                        $guesses[$proposal] = $distance;
                        $minScore = $distance;
                    }
                }
            }
            $msg = sprintf('Unrecognized option%s "%s" under "%s"', (1 === \count($value)) ? '' : 's', implode(', ', array_keys($value)), $this->getPath());
            if (\count($guesses)) {
                asort($guesses);
                $msg .= sprintf('. Did you mean "%s"?', implode('", "', array_keys($guesses)));
            } else {
                $msg .= sprintf('. Available option%s %s "%s".', (1 === \count($proposals)) ? '' : 's', (1 === \count($proposals)) ? 'is' : 'are', implode('", "', $proposals));
            }
            $ex = new InvalidConfigurationException($msg);
            $ex->setPath($this->getPath());
            throw $ex;
        }
        return $normalized;
    }
    /**
     * @param mixed[] $value
     */
    protected function remapXml($value): array
    {
        foreach ($this->xmlRemappings as [$singular, $plural]) {
            if (!isset($value[$singular])) {
                continue;
            }
            $value[$plural] = Processor::normalizeConfig($value, $singular, $plural);
            unset($value[$singular]);
        }
        return $value;
    }
    /**
     * @param mixed $leftSide
     * @param mixed $rightSide
     * @return mixed
     */
    protected function mergeValues($leftSide, $rightSide)
    {
        if (\false === $rightSide) {
            return \false;
        }
        if (\false === $leftSide || !$this->performDeepMerging) {
            return $rightSide;
        }
        foreach ($rightSide as $k => $v) {
            if (!\array_key_exists($k, $leftSide)) {
                if (!$this->allowNewKeys) {
                    $ex = new InvalidConfigurationException(sprintf('You are not allowed to define new elements for path "%s". Please define all elements for this path in one config file. If you are trying to overwrite an element, make sure you redefine it with the same name.', $this->getPath()));
                    $ex->setPath($this->getPath());
                    throw $ex;
                }
                $leftSide[$k] = $v;
                continue;
            }
            if (!isset($this->children[$k])) {
                if (!$this->ignoreExtraKeys || $this->removeExtraKeys) {
                    throw new RuntimeException('merge() expects a normalized config array.');
                }
                $leftSide[$k] = $v;
                continue;
            }
            $leftSide[$k] = $this->children[$k]->merge($leftSide[$k], $v);
        }
        return $leftSide;
    }
    protected function allowPlaceholders(): bool
    {
        return \false;
    }
}
