<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting;

use Staatic\WordPress\Service\PartialRenderer;

abstract class AbstractSetting implements SettingInterface, RendersPartialsInterface
{
    /**
     * @var PartialRenderer
     */
    protected $renderer;

    /**
     * @param PartialRenderer $renderer
     */
    public function setPartialRenderer($renderer): void
    {
        $this->renderer = $renderer;
    }

    public function extendedLabel(): ?string
    {
        return null;
    }

    public function description(): ?string
    {
        return null;
    }

    public function isEnabled(): bool
    {
        return \true;
    }

    public function value()
    {
        $value = get_option($this->name(), $this->defaultValue());
        if ($value === null) {
            return null;
        }
        switch ($this->type()) {
            case self::TYPE_BOOLEAN:
                return (bool) $value;
            case self::TYPE_INTEGER:
                return (int) $value;
            case self::TYPE_STRING:
                return (string) $value;
            default:
                return $value;
        }
    }

    public function defaultValue()
    {
        return null;
    }

    public function sanitizeValue($value)
    {
        return $value;
    }

    protected function template(): string
    {
        return $this->type();
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        $path = sprintf('admin/settings/%s.php', $this->template());
        $this->renderer->render($path, [
            'setting' => $this,
            'attributes' => $attributes
        ]);
    }

    /**
     * @param mixed[] $examples
     */
    protected function examplesList($examples): string
    {
        return sprintf('%s: <code>%s</code>.', __('Examples', 'staatic'), implode('</code>, <code>', $examples));
    }
}
