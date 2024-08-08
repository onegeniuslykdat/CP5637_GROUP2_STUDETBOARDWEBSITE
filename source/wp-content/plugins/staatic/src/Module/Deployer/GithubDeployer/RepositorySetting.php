<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\GithubDeployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class RepositorySetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_github_repository';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Repository', 'staatic');
    }

    public function description(): ?string
    {
        return sprintf(
            /* translators: 1: Example repository. */
            __('The name of the Git repository to store the static site\'s data, in the format <code>OWNER/REPO</code>.<br>Examples: %1$s.', 'staatic'),
            implode(', ', ['<code>staatic/wordpress-site</code>', '<code>staatic/staatic.github.io</code>'])
        );
    }
}
