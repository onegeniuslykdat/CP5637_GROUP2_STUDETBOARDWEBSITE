<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
class Alias
{
    private const DEFAULT_DEPRECATION_TEMPLATE = 'The "%alias_id%" service alias is deprecated. You should stop using it, as it will be removed in the future.';
    /**
     * @var string
     */
    private $id;
    /**
     * @var bool
     */
    private $public;
    /**
     * @var mixed[]
     */
    private $deprecation = [];
    public function __construct(string $id, bool $public = \false)
    {
        $this->id = $id;
        $this->public = $public;
    }
    public function isPublic(): bool
    {
        return $this->public;
    }
    /**
     * @param bool $boolean
     * @return static
     */
    public function setPublic($boolean)
    {
        $this->public = $boolean;
        return $this;
    }
    public function isPrivate(): bool
    {
        return !$this->public;
    }
    /**
     * @param string $package
     * @param string $version
     * @param string $message
     * @return static
     */
    public function setDeprecated($package, $version, $message)
    {
        if ('' !== $message) {
            if (preg_match('#[\r\n]|\*/#', $message)) {
                throw new InvalidArgumentException('Invalid characters found in deprecation template.');
            }
            if (strpos($message, '%alias_id%') === false) {
                throw new InvalidArgumentException('The deprecation template must contain the "%alias_id%" placeholder.');
            }
        }
        $this->deprecation = ['package' => $package, 'version' => $version, 'message' => $message ?: self::DEFAULT_DEPRECATION_TEMPLATE];
        return $this;
    }
    public function isDeprecated(): bool
    {
        return (bool) $this->deprecation;
    }
    /**
     * @param string $id
     */
    public function getDeprecation($id): array
    {
        return ['package' => $this->deprecation['package'], 'version' => $this->deprecation['version'], 'message' => str_replace('%alias_id%', $id, $this->deprecation['message'])];
    }
    public function __toString(): string
    {
        return $this->id;
    }
}
