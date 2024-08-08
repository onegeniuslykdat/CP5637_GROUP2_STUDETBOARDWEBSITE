<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\FilesystemDeployer;

use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Util\WordpressEnv;

final class TargetDirectorySetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_filesystem_target_directory';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Target Directory', 'staatic');
    }

    public function description(): ?string
    {
        return __('The path to the directory on the filesystem where the static version of your site is deployed.', 'staatic');
    }

    public function sanitizeValue($value)
    {
        $path = untrailingslashit(wp_normalize_path($value));
        $wordpressPath = wp_normalize_path(WordpressEnv::getWordpressPath());
        if ($realPath = realpath($path)) {
            $path = $realPath;
        }
        if ($realWordpressPath = realpath($wordpressPath)) {
            $wordpressPath = $realWordpressPath;
        }
        if (strncmp($wordpressPath, $path, strlen($path)) === 0) {
            add_settings_error('staatic-settings', 'invalid_filesystem_target_directory', sprintf(
                /* translators: 1: Supplied target directory, 2: WordPress directory. */
                __('The chosen target directory ("%1$s") is at the same level or above the WordPress installation directory ("%2$s"). Publishing here risks overwriting your WordPress files and potentially other critical data. To ensure the safety of your WordPress installation and related content, please select a different target directory for publishing your static site.', 'staatic'),
                esc_html($value),
                esc_html($wordpressPath)
            ));

            return $this->defaultValue();
        }

        return $path;
    }

    public function defaultValue()
    {
        return WordpressEnv::getUploadsPath() . '/staatic/deploy';
    }
}
