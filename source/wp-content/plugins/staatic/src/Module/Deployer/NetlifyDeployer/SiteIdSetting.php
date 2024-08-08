<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\NetlifyDeployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class SiteIdSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_netlify_site_id';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Netlify Site ID', 'staatic');
    }

    public function description(): ?string
    {
        return sprintf(
            /* translators: 1: Link to Netlify App. */
            __('You can find your Netlify Site (API) ID on the <a href="%1$s" target="_blank" rel="noopener">Netlify App</a> at Site Settings > Site Details > Site ID.', 'staatic'),
            'https://app.netlify.com/'
        );
    }
}
