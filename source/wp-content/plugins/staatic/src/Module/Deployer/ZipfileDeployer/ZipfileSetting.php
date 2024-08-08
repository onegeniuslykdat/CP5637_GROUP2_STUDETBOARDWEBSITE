<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\ZipfileDeployer;

use Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage;
use Staatic\WordPress\Setting\AbstractSetting;

final class ZipfileSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_zipfile_dummy';
    }

    public function type(): string
    {
        return self::TYPE_COMPOSED;
    }

    public function label(): string
    {
        return __('Download Zipfile', 'staatic');
    }

    public function description(): ?string
    {
        return sprintf(
            /* translators: 1: Download label translated, 2: Link to Publication List. */
            __('Download the relevant publication from the <a href="%2$s">publication overview</a> by choosing <em>%1$s</em>.', 'staatic'),
            __('Download', 'staatic'),
            admin_url(sprintf('admin.php?page=%s', PublicationsPage::PAGE_SLUG))
        );
    }

    public function settings(): array
    {
        return [];
    }
}
