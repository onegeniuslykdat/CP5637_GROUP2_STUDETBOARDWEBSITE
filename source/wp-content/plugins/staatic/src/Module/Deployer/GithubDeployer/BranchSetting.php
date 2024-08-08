<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\GithubDeployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class BranchSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_github_branch';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Branch', 'staatic');
    }

    public function description(): ?string
    {
        return __('The name of the Git branch to commit to.<br>Example: <code>main</code>.', 'staatic');
    }

    public function defaultValue()
    {
        return 'main';
    }
}
