<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use InvalidArgumentException;
use Staatic\WordPress\Factory\EncrypterFactory;
use Staatic\WordPress\Service\Encrypter\InvalidValueException;
use Staatic\WordPress\Service\Encrypter\PossiblyUnencryptedValueException;
use Staatic\WordPress\Setting\ActsOnUpdateInterface;
use Staatic\WordPress\Setting\ComposedSettingInterface;
use Staatic\WordPress\Setting\RendersPartialsInterface;
use Staatic\WordPress\Setting\SettingInterface;
use Staatic\WordPress\Setting\StoresEncryptedInterface;
use Staatic\WordPress\SettingGroup\SettingGroupInterface;

final class Settings
{
    /**
     * @var PartialRenderer
     */
    private $renderer;

    /**
     * @var EncrypterFactory
     */
    private $encrypterFactory;

    /** @var SettingGroupInterface[] */
    private $groups = [];

    /** @var SettingInterface[] */
    private $settings = [];

    /**
     * @var mixed[]
     */
    private $settingsToGroups = [];

    public function __construct(PartialRenderer $renderer, EncrypterFactory $encrypterFactory)
    {
        $this->renderer = $renderer;
        $this->encrypterFactory = $encrypterFactory;
    }

    public function addGroup(SettingGroupInterface $group): void
    {
        // Allow replace, so no checking whether group exists...
        $this->groups[$group->name()] = $group;
        uasort($this->groups, function (SettingGroupInterface $a, SettingGroupInterface $b) {
            return $a->position() <=> $b->position();
        });
    }

    public function addSetting(string $groupName, SettingInterface $setting): void
    {
        $this->settings[$setting->name()] = $setting;
        $this->settingsToGroups[$setting->name()] = $groupName;
    }

    public function registerSettings(): void
    {
        foreach ($this->settings as $settingName => $setting) {
            $groupName = $this->settingsToGroups[$settingName];
            if ($setting instanceof RendersPartialsInterface) {
                $setting->setPartialRenderer($this->renderer);
            }
            if ($setting instanceof ComposedSettingInterface) {
                $settings = $setting->settings();
            } else {
                $settings = [$setting];
            }
            foreach ($settings as $innerSetting) {
                if ($innerSetting instanceof RendersPartialsInterface) {
                    $innerSetting->setPartialRenderer($this->renderer);
                }
                register_setting($groupName, $innerSetting->name(), [
                    'type' => $innerSetting->type(),
                    'description' => $innerSetting->description(),
                    'sanitize_callback' => [$innerSetting, 'sanitizeValue'],
                    'default' => $innerSetting->defaultValue()
                ]);
                if ($innerSetting instanceof StoresEncryptedInterface) {
                    add_filter("option_{$innerSetting->name()}", function ($value) {
                        return $this->decryptOptionValue($value);
                    }, \PHP_INT_MIN);
                    add_filter("pre_update_option_{$innerSetting->name()}", function ($value) {
                        return $this->encryptOptionValue($value);
                    }, \PHP_INT_MAX);
                }
                if ($innerSetting instanceof ActsOnUpdateInterface) {
                    add_action("add_option_{$innerSetting->name()}", function ($option, $value) use ($innerSetting) {
                        if ($innerSetting instanceof StoresEncryptedInterface) {
                            $value = $this->decryptOptionValue($value);
                        }
                        $innerSetting->onUpdate($value, null);
                    }, 10, 2);
                    add_action("update_option_{$innerSetting->name()}", function ($oldValue, $value, $option) use (
                        $innerSetting
                    ) {
                        if ($innerSetting instanceof StoresEncryptedInterface) {
                            $value = $this->decryptOptionValue($value);
                        }
                        $innerSetting->onUpdate($value, $oldValue);
                    }, 10, 3);
                }
            }
        }
    }

    private function decryptOptionValue($value)
    {
        if (empty($value)) {
            return $value;
        }

        try {
            $result = ($this->encrypterFactory)()->decrypt($value);
        } catch (PossiblyUnencryptedValueException $exception) {
            // The option value could be unencrypted (legacy option).
            // Simply return the original option value.
            return $value;
        } catch (InvalidValueException $exception) {
            return '';
        }

        return $result;
    }

    private function encryptOptionValue($value): string
    {
        return ($this->encrypterFactory)()->encrypt($value);
    }

    /**
     * @return SettingGroupInterface[]
     */
    public function groups(): array
    {
        return $this->groups;
    }

    public function group(string $name): SettingGroupInterface
    {
        if (!isset($this->groups[$name])) {
            throw new InvalidArgumentException("Setting group '{$name}' does not exist");
        }

        return $this->groups[$name];
    }

    /**
     * @return SettingInterface[]
     */
    public function settings(?string $groupName = null): array
    {
        $settings = $this->settings;
        if ($groupName) {
            $settings = array_filter($settings, function (SettingInterface $setting) use ($groupName) {
                return $this->settingsToGroups[$setting->name()] === $groupName;
            });
        }

        return $settings;
    }

    public function settingsApiInit(): void
    {
        foreach ($this->groups as $groupName => $group) {
            $groupPageId = sprintf('%s-settings-page', $groupName);
            $groupSectionId = sprintf('%s-settings-section', $groupName);
            add_settings_section(
                $groupSectionId,
                '',
                // $groupLabel,
                [$group, 'render'],
                $groupPageId
            );
            foreach ($this->settings($groupName) as $setting) {
                if (!$setting->isEnabled()) {
                    continue;
                }
                add_settings_field(
                    $setting->name(),
                    $setting->label(),
                    [$setting, 'render'],
                    $groupPageId,
                    $groupSectionId,
                    [
                    'class' => sprintf('%s %s', $groupName, $setting->name())
                
                ]);
            }
        }
    }

    public function renderErrors(): string
    {
        ob_start();
        settings_errors('staatic-settings');
        $errors = ob_get_clean();

        return $errors;
    }

    public function renderHiddenFields(string $groupName): string
    {
        ob_start();
        settings_fields($groupName);
        $hiddenFields = ob_get_clean();

        return $hiddenFields;
    }

    public function renderSettings(string $groupName): string
    {
        $groupPageId = sprintf('%s-settings-page', $groupName);
        ob_start();
        do_settings_sections($groupPageId);
        $settings = ob_get_clean();

        return $settings;
    }

    public function hasSettings(string $groupName): bool
    {
        $groupsWithSettings = array_unique(array_values($this->settingsToGroups));

        return in_array($groupName, $groupsWithSettings);
    }
}
