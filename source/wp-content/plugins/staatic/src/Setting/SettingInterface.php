<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting;

interface SettingInterface
{
    public const TYPE_BOOLEAN = 'boolean';

    public const TYPE_INTEGER = 'integer';

    public const TYPE_NUMBER = 'number';

    public const TYPE_STRING = 'string';

    public const TYPE_ARRAY = 'array';

    public const TYPE_OBJECT = 'object';

    public const TYPE_COMPOSED = 'composed';

    //!
    public function name(): string;

    public function type(): string;

    public function label(): string;

    public function extendedLabel(): ?string;

    public function description(): ?string;

    public function isEnabled(): bool;

    public function value();

    public function defaultValue();

    public function sanitizeValue($value);

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void;
}
