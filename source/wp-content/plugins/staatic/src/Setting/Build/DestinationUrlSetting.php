<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Build;

use Staatic\Vendor\GuzzleHttp\Psr7\Exception\MalformedUriException;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\WordPress\Setting\AbstractSetting;

final class DestinationUrlSetting extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_destination_url';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Destination URL', 'staatic');
    }

    public function description(): ?string
    {
        return __('The destination URL determines how links within your static site are constructed.', 'staatic');
    }

    public function defaultValue()
    {
        return '/';
    }

    public function sanitizeValue($value)
    {
        try {
            $url = new Uri($value);
        } catch (MalformedUriException $e) {
            add_settings_error('staatic-settings', 'invalid_destination_url', sprintf(
                /* translators: %s: Destination URL. */
                __('The specified destination URL "%s" is invalid; using "/" instead.', 'staatic'),
                esc_html($value)
            ));

            return '/';
        }

        return $value;
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        $this->renderer->render('admin/settings/destination_url.php', [
            'setting' => $this,
            'attributes' => $attributes
        ]);
    }
}
