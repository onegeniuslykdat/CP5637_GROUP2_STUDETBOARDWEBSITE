<?php

declare(strict_types=1);

namespace Staatic\WordPress\SettingGroup;

interface SettingGroupInterface
{
    public function name(): string;

    public function label(): string;

    public function position(): int;

    public function descriptionCallback();

    public function render(): void;

    /**
     * @param string $label
     */
    public function setLabel($label): void;

    /**
     * @param int $position
     */
    public function setPosition($position): void;

    public function setDescriptionCallback($descriptionCallback): void;
}
