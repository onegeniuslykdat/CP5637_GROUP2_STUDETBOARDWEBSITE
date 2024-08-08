<?php

declare(strict_types=1);

namespace Staatic\WordPress\SettingGroup;

final class SettingGroup implements SettingGroupInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $position;

    private $descriptionCallback = null;

    public function __construct(string $name, string $label, int $position, $descriptionCallback = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->position = $position;
        $this->descriptionCallback = $descriptionCallback;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function descriptionCallback()
    {
        return $this->descriptionCallback;
    }

    public function render(): void
    {
        if ($this->descriptionCallback) {
            echo ($this->descriptionCallback)();
        }
    }

    /**
     * @param string $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    /**
     * @param int $position
     */
    public function setPosition($position): void
    {
        $this->position = $position;
    }

    public function setDescriptionCallback($descriptionCallback): void
    {
        $this->descriptionCallback = $descriptionCallback;
    }
}
