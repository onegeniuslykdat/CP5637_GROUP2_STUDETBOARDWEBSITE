<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Dumper;

use UnexpectedValueException;
use Closure;
use Staatic\Vendor\Symfony\Component\Config\Definition\ArrayNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\BaseNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\ConfigurationInterface;
use Staatic\Vendor\Symfony\Component\Config\Definition\EnumNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\NodeInterface;
use Staatic\Vendor\Symfony\Component\Config\Definition\PrototypedArrayNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\ScalarNode;
use Staatic\Vendor\Symfony\Component\Yaml\Inline;
class YamlReferenceDumper
{
    /**
     * @var string|null
     */
    private $reference;
    /**
     * @param ConfigurationInterface $configuration
     */
    public function dump($configuration)
    {
        return $this->dumpNode($configuration->getConfigTreeBuilder()->buildTree());
    }
    /**
     * @param ConfigurationInterface $configuration
     * @param string $path
     */
    public function dumpAtPath($configuration, $path)
    {
        $rootNode = $node = $configuration->getConfigTreeBuilder()->buildTree();
        foreach (explode('.', $path) as $step) {
            if (!$node instanceof ArrayNode) {
                throw new UnexpectedValueException(sprintf('Unable to find node at path "%s.%s".', $rootNode->getName(), $path));
            }
            $children = ($node instanceof PrototypedArrayNode) ? $this->getPrototypeChildren($node) : $node->getChildren();
            foreach ($children as $child) {
                if ($child->getName() === $step) {
                    $node = $child;
                    continue 2;
                }
            }
            throw new UnexpectedValueException(sprintf('Unable to find node at path "%s.%s".', $rootNode->getName(), $path));
        }
        return $this->dumpNode($node);
    }
    /**
     * @param NodeInterface $node
     */
    public function dumpNode($node)
    {
        $this->reference = '';
        $this->writeNode($node);
        $ref = $this->reference;
        $this->reference = null;
        return $ref;
    }
    private function writeNode(NodeInterface $node, ?NodeInterface $parentNode = null, int $depth = 0, bool $prototypedArray = \false): void
    {
        $comments = [];
        $default = '';
        $defaultArray = null;
        $children = null;
        $example = null;
        if ($node instanceof BaseNode) {
            $example = $node->getExample();
        }
        if ($node instanceof ArrayNode) {
            $children = $node->getChildren();
            if ($node instanceof PrototypedArrayNode) {
                $children = $this->getPrototypeChildren($node);
            }
            if (!$children && !($node->hasDefaultValue() && \count($defaultArray = $node->getDefaultValue()))) {
                $default = '[]';
            }
        } elseif ($node instanceof EnumNode) {
            $comments[] = 'One of ' . $node->getPermissibleValues('; ');
            $default = $node->hasDefaultValue() ? Inline::dump($node->getDefaultValue()) : '~';
        } else {
            $default = '~';
            if ($node->hasDefaultValue()) {
                $default = $node->getDefaultValue();
                if (\is_array($default)) {
                    if (\count($defaultArray = $node->getDefaultValue())) {
                        $default = '';
                    } elseif (!\is_array($example)) {
                        $default = '[]';
                    }
                } else {
                    $default = Inline::dump($default);
                }
            }
        }
        if ($node->isRequired()) {
            $comments[] = 'Required';
        }
        if ($node instanceof BaseNode && $node->isDeprecated()) {
            $deprecation = $node->getDeprecation($node->getName(), $parentNode ? $parentNode->getPath() : $node->getPath());
            $comments[] = sprintf('Deprecated (%s)', (($deprecation['package'] || $deprecation['version']) ? "Since {$deprecation['package']} {$deprecation['version']}: " : '') . $deprecation['message']);
        }
        if ($example && !\is_array($example)) {
            $comments[] = 'Example: ' . Inline::dump($example);
        }
        $default = ('' != (string) $default) ? ' ' . $default : '';
        $comments = \count($comments) ? '# ' . implode(', ', $comments) : '';
        $key = $prototypedArray ? '-' : ($node->getName() . ':');
        $text = rtrim(sprintf('%-21s%s %s', $key, $default, $comments), ' ');
        if ($node instanceof BaseNode && $info = $node->getInfo()) {
            $this->writeLine('');
            $info = str_replace("\n", sprintf("\n%" . $depth * 4 . 's# ', ' '), $info);
            $this->writeLine('# ' . $info, $depth * 4);
        }
        $this->writeLine($text, $depth * 4);
        if ($defaultArray) {
            $this->writeLine('');
            $message = (\count($defaultArray) > 1) ? 'Defaults' : 'Default';
            $this->writeLine('# ' . $message . ':', $depth * 4 + 4);
            $this->writeArray($defaultArray, $depth + 1);
        }
        if (\is_array($example)) {
            $this->writeLine('');
            $message = (\count($example) > 1) ? 'Examples' : 'Example';
            $this->writeLine('# ' . $message . ':', $depth * 4 + 4);
            $this->writeArray(array_map(Closure::fromCallable([Inline::class, 'dump']), $example), $depth + 1, \true);
        }
        if ($children) {
            foreach ($children as $childNode) {
                $this->writeNode($childNode, $node, $depth + 1, $node instanceof PrototypedArrayNode && !$node->getKeyAttribute());
            }
        }
    }
    private function writeLine(string $text, int $indent = 0): void
    {
        $indent = \strlen($text) + $indent;
        $format = '%' . $indent . 's';
        $this->reference .= sprintf($format, $text) . "\n";
    }
    private function writeArray(array $array, int $depth, bool $asComment = \false): void
    {
        $arrayIsListFunction = function (array $array) : bool {
            if (function_exists('array_is_list')) {
                return array_is_list($array);
            }
            if ($array === []) {
                return true;
            }
            $current_key = 0;
            foreach ($array as $key => $noop) {
                if ($key !== $current_key) {
                    return false;
                }
                ++$current_key;
            }
            return true;
        };
        $isIndexed = $arrayIsListFunction($array);
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $val = '';
            } else {
                $val = $value;
            }
            $prefix = $asComment ? '# ' : '';
            if ($isIndexed) {
                $this->writeLine($prefix . '- ' . $val, $depth * 4);
            } else {
                $this->writeLine(sprintf('%s%-20s %s', $prefix, $key . ':', $val), $depth * 4);
            }
            if (\is_array($value)) {
                $this->writeArray($value, $depth + 1, $asComment);
            }
        }
    }
    private function getPrototypeChildren(PrototypedArrayNode $node): array
    {
        $prototype = $node->getPrototype();
        $key = $node->getKeyAttribute();
        if (!$key && !$prototype instanceof ArrayNode) {
            return $node->getChildren();
        }
        if ($prototype instanceof ArrayNode) {
            $keyNode = new ArrayNode($key, $node);
            $children = $prototype->getChildren();
            if ($prototype instanceof PrototypedArrayNode && $prototype->getKeyAttribute()) {
                $children = $this->getPrototypeChildren($prototype);
            }
            foreach ($children as $childNode) {
                $keyNode->addChild($childNode);
            }
        } else {
            $keyNode = new ScalarNode($key, $node);
        }
        $info = 'Prototype';
        if (null !== $prototype->getInfo()) {
            $info .= ': ' . $prototype->getInfo();
        }
        $keyNode->setInfo($info);
        return [$key => $keyNode];
    }
}
