<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\GithubDeployer;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\ComposedSettingInterface;

final class GitSetting extends AbstractSetting implements ComposedSettingInterface
{
    /**
     * @var ServiceLocator
     */
    private $settingLocator;

    public function __construct(ServiceLocator $settingLocator)
    {
        $this->settingLocator = $settingLocator;
    }

    public function name(): string
    {
        return 'staatic_github_git';
    }

    public function type(): string
    {
        return self::TYPE_COMPOSED;
    }

    public function label(): string
    {
        return __('Git', 'staatic');
    }

    public function settings(): array
    {
        return [
            $this->settingLocator->get(RepositorySetting::class),
            $this->settingLocator->get(BranchSetting::class),
            $this->settingLocator->get(CommitMessageSetting::class),
            $this->settingLocator->get(PrefixSetting::class),
            $this->settingLocator->get(RetainPathsSetting::class)
        ];
    }
}
