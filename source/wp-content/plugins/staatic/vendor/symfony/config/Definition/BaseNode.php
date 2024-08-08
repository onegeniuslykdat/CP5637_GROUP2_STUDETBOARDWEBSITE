<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\Exception;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\UnsetKeyException;
abstract class BaseNode implements NodeInterface
{
    public const DEFAULT_PATH_SEPARATOR = '.';
    /**
     * @var mixed[]
     */
    private static $placeholderUniquePrefixes = [];
    /**
     * @var mixed[]
     */
    private static $placeholders = [];
    protected $name;
    protected $parent;
    protected $normalizationClosures = [];
    protected $normalizedTypes = [];
    protected $finalValidationClosures = [];
    protected $allowOverwrite = \true;
    protected $required = \false;
    protected $deprecation = [];
    protected $equivalentValues = [];
    protected $attributes = [];
    protected $pathSeparator;
    /**
     * @var mixed
     */
    private $handlingPlaceholder = null;
    public function __construct(?string $name, ?NodeInterface $parent = null, string $pathSeparator = self::DEFAULT_PATH_SEPARATOR)
    {
        if (strpos($name = (string) $name, $pathSeparator) !== false) {
            throw new InvalidArgumentException('The name must not contain ".' . $pathSeparator . '".');
        }
        $this->name = $name;
        $this->parent = $parent;
        $this->pathSeparator = $pathSeparator;
    }
    /**
     * @param string $placeholder
     * @param mixed[] $values
     */
    public static function setPlaceholder($placeholder, $values): void
    {
        if (!$values) {
            throw new InvalidArgumentException('At least one value must be provided.');
        }
        self::$placeholders[$placeholder] = $values;
    }
    /**
     * @param string $prefix
     */
    public static function setPlaceholderUniquePrefix($prefix): void
    {
        self::$placeholderUniquePrefixes[] = $prefix;
    }
    public static function resetPlaceholders(): void
    {
        self::$placeholderUniquePrefixes = [];
        self::$placeholders = [];
    }
    /**
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }
    /**
     * @param string $key
     */
    public function hasAttribute($key): bool
    {
        return isset($this->attributes[$key]);
    }
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    /**
     * @param mixed[] $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }
    /**
     * @param string $key
     */
    public function removeAttribute($key)
    {
        unset($this->attributes[$key]);
    }
    /**
     * @param string $info
     */
    public function setInfo($info)
    {
        $this->setAttribute('info', $info);
    }
    public function getInfo(): ?string
    {
        return $this->getAttribute('info');
    }
    /**
     * @param string|mixed[] $example
     */
    public function setExample($example)
    {
        $this->setAttribute('example', $example);
    }
    /**
     * @return mixed[]|string|null
     */
    public function getExample()
    {
        return $this->getAttribute('example');
    }
    /**
     * @param mixed $originalValue
     * @param mixed $equivalentValue
     */
    public function addEquivalentValue($originalValue, $equivalentValue)
    {
        $this->equivalentValues[] = [$originalValue, $equivalentValue];
    }
    /**
     * @param bool $boolean
     */
    public function setRequired($boolean)
    {
        $this->required = $boolean;
    }
    /**
     * @param string $package
     * @param string $version
     * @param string $message
     */
    public function setDeprecated($package, $version, $message = 'The child node "%node%" at path "%path%" is deprecated.')
    {
        $this->deprecation = ['package' => $package, 'version' => $version, 'message' => $message];
    }
    /**
     * @param bool $allow
     */
    public function setAllowOverwrite($allow)
    {
        $this->allowOverwrite = $allow;
    }
    /**
     * @param mixed[] $closures
     */
    public function setNormalizationClosures($closures)
    {
        $this->normalizationClosures = $closures;
    }
    /**
     * @param mixed[] $types
     */
    public function setNormalizedTypes($types)
    {
        $this->normalizedTypes = $types;
    }
    public function getNormalizedTypes(): array
    {
        return $this->normalizedTypes;
    }
    /**
     * @param mixed[] $closures
     */
    public function setFinalValidationClosures($closures)
    {
        $this->finalValidationClosures = $closures;
    }
    public function isRequired(): bool
    {
        return $this->required;
    }
    public function isDeprecated(): bool
    {
        return (bool) $this->deprecation;
    }
    /**
     * @param string $node
     * @param string $path
     */
    public function getDeprecation($node, $path): array
    {
        return ['package' => $this->deprecation['package'], 'version' => $this->deprecation['version'], 'message' => strtr($this->deprecation['message'], ['%node%' => $node, '%path%' => $path])];
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getPath(): string
    {
        if (null !== $this->parent) {
            return $this->parent->getPath() . $this->pathSeparator . $this->name;
        }
        return $this->name;
    }
    /**
     * @param mixed $leftSide
     * @param mixed $rightSide
     * @return mixed
     */
    final public function merge($leftSide, $rightSide)
    {
        if (!$this->allowOverwrite) {
            throw new ForbiddenOverwriteException(sprintf('Configuration path "%s" cannot be overwritten. You have to define all options for this path, and any of its sub-paths in one configuration section.', $this->getPath()));
        }
        if ($leftSide !== $leftPlaceholders = self::resolvePlaceholderValue($leftSide)) {
            foreach ($leftPlaceholders as $leftPlaceholder) {
                $this->handlingPlaceholder = $leftSide;
                try {
                    $this->merge($leftPlaceholder, $rightSide);
                } finally {
                    $this->handlingPlaceholder = null;
                }
            }
            return $rightSide;
        }
        if ($rightSide !== $rightPlaceholders = self::resolvePlaceholderValue($rightSide)) {
            foreach ($rightPlaceholders as $rightPlaceholder) {
                $this->handlingPlaceholder = $rightSide;
                try {
                    $this->merge($leftSide, $rightPlaceholder);
                } finally {
                    $this->handlingPlaceholder = null;
                }
            }
            return $rightSide;
        }
        $this->doValidateType($leftSide);
        $this->doValidateType($rightSide);
        return $this->mergeValues($leftSide, $rightSide);
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    final public function normalize($value)
    {
        $value = $this->preNormalize($value);
        foreach ($this->normalizationClosures as $closure) {
            $value = $closure($value);
        }
        if ($value !== $placeholders = self::resolvePlaceholderValue($value)) {
            foreach ($placeholders as $placeholder) {
                $this->handlingPlaceholder = $value;
                try {
                    $this->normalize($placeholder);
                } finally {
                    $this->handlingPlaceholder = null;
                }
            }
            return $value;
        }
        foreach ($this->equivalentValues as $data) {
            if ($data[0] === $value) {
                $value = $data[1];
            }
        }
        $this->doValidateType($value);
        return $this->normalizeValue($value);
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    protected function preNormalize($value)
    {
        return $value;
    }
    public function getParent(): ?NodeInterface
    {
        return $this->parent;
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    final public function finalize($value)
    {
        if ($value !== $placeholders = self::resolvePlaceholderValue($value)) {
            foreach ($placeholders as $placeholder) {
                $this->handlingPlaceholder = $value;
                try {
                    $this->finalize($placeholder);
                } finally {
                    $this->handlingPlaceholder = null;
                }
            }
            return $value;
        }
        $this->doValidateType($value);
        $value = $this->finalizeValue($value);
        foreach ($this->finalValidationClosures as $closure) {
            try {
                $value = $closure($value);
            } catch (Exception $e) {
                if ($e instanceof UnsetKeyException && null !== $this->handlingPlaceholder) {
                    continue;
                }
                throw $e;
            } catch (\Exception $e) {
                throw new InvalidConfigurationException(sprintf('Invalid configuration for path "%s": ', $this->getPath()) . $e->getMessage(), $e->getCode(), $e);
            }
        }
        return $value;
    }
    /**
     * @param mixed $value
     */
    abstract protected function validateType($value);
    /**
     * @param mixed $value
     * @return mixed
     */
    abstract protected function normalizeValue($value);
    /**
     * @param mixed $leftSide
     * @param mixed $rightSide
     * @return mixed
     */
    abstract protected function mergeValues($leftSide, $rightSide);
    /**
     * @param mixed $value
     * @return mixed
     */
    abstract protected function finalizeValue($value);
    protected function allowPlaceholders(): bool
    {
        return \true;
    }
    protected function isHandlingPlaceholder(): bool
    {
        return null !== $this->handlingPlaceholder;
    }
    protected function getValidPlaceholderTypes(): array
    {
        return [];
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    private static function resolvePlaceholderValue($value)
    {
        if (\is_string($value)) {
            if (isset(self::$placeholders[$value])) {
                return self::$placeholders[$value];
            }
            foreach (self::$placeholderUniquePrefixes as $placeholderUniquePrefix) {
                if (strncmp($value, $placeholderUniquePrefix, strlen($placeholderUniquePrefix)) === 0) {
                    return [];
                }
            }
        }
        return $value;
    }
    /**
     * @param mixed $value
     */
    private function doValidateType($value): void
    {
        if (null !== $this->handlingPlaceholder && !$this->allowPlaceholders()) {
            $e = new InvalidTypeException(sprintf('A dynamic value is not compatible with a "%s" node type at path "%s".', static::class, $this->getPath()));
            $e->setPath($this->getPath());
            throw $e;
        }
        if (null === $this->handlingPlaceholder || null === $value) {
            $this->validateType($value);
            return;
        }
        $knownTypes = array_keys(self::$placeholders[$this->handlingPlaceholder]);
        $validTypes = $this->getValidPlaceholderTypes();
        if ($validTypes && array_diff($knownTypes, $validTypes)) {
            $e = new InvalidTypeException(sprintf('Invalid type for path "%s". Expected %s, but got %s.', $this->getPath(), (1 === \count($validTypes)) ? '"' . reset($validTypes) . '"' : ('one of "' . implode('", "', $validTypes) . '"'), (1 === \count($knownTypes)) ? '"' . reset($knownTypes) . '"' : ('one of "' . implode('", "', $knownTypes) . '"')));
            if ($hint = $this->getInfo()) {
                $e->addHint($hint);
            }
            $e->setPath($this->getPath());
            throw $e;
        }
        $this->validateType($value);
    }
}
