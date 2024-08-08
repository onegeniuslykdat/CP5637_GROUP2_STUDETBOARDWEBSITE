<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Build;

use Staatic\WordPress\Service\AdditionalRedirects;
use Staatic\WordPress\Setting\AbstractSetting;

final class AdditionalRedirectsSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_additional_redirects';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Additional Redirects', 'staatic');
    }

    public function description(): ?string
    {
        return sprintf(
            /* translators: %s: Example additional redirects. */
            __('Optionally add redirects that need to be included in the build.<br>%s', 'staatic'),
            $this->examplesList(
                ['/old-post /new-post 301', '/some-other-post https://othersite.example/some-other-post 302']
            )
        );
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        $this->renderer->render('admin/settings/additional_redirects.php', [
            'setting' => $this,
            'attributes' => $attributes
        ]);
    }

    public function sanitizeValue($value)
    {
        $result = AdditionalRedirects::validateAndResolve($value);
        foreach ($result['errors']->get_error_messages() as $message) {
            add_settings_error('staatic-settings', 'additional_redirects', __('Skipped: ', 'staatic') . $message);
        }

        return $result['newValue'];
    }
}
