<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\GithubDeployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class CommitMessageSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_github_commit_message';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Commit Message', 'staatic');
    }

    public function description(): ?string
    {
        return __('The commit message to use when applying updates to the repository.<br>Example: <code>Deployment {shortId}</code>.', 'staatic');
    }

    public function defaultValue()
    {
        return 'Staatic deployment {shortId}';
    }
}
