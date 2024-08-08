<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\NetlifyDeployer;

use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\ComposedSettingInterface;

final class AuthSetting extends AbstractSetting implements ComposedSettingInterface
{
    /**
     * @var AccessTokenSetting
     */
    private $token;

    /**
     * @var SiteIdSetting
     */
    private $siteId;

    public function __construct(AccessTokenSetting $token, SiteIdSetting $siteId)
    {
        $this->token = $token;
        $this->siteId = $siteId;
    }

    public function name(): string
    {
        return 'staatic_netlify_auth';
    }

    public function type(): string
    {
        return self::TYPE_COMPOSED;
    }

    public function label(): string
    {
        return __('Authentication', 'staatic');
    }

    public function description(): ?string
    {
        return sprintf(
            /* translators: 1: Link to Netlify Documentation. */
            __('You can find or create your Netlify (Personal) Access Token <a href="%1$s" target="_blank" rel="noopener">here</a>.', 'staatic'),
            'https://app.netlify.com/user/applications#personal-access-tokens'
        );
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        parent::render();
        if ($this->token->value()) {
            echo '<div
                data-staatic-component="NetlifyStatus"
                data-token="' . esc_attr($this->token->name()) . '"
                data-site-id="' . esc_attr($this->siteId->name()) . '"
                style="margin-top: 10px;"
            ></div>';
        }
    }

    public function settings(): array
    {
        return [$this->token];
    }
}
