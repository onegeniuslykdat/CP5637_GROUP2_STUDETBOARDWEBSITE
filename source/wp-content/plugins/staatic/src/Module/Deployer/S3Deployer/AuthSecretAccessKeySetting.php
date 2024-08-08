<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\WordPress\Setting\AbstractSetting;
use Staatic\WordPress\Setting\StoresEncryptedInterface;

final class AuthSecretAccessKeySetting extends AbstractSetting implements StoresEncryptedInterface
{
    public function name(): string
    {
        return 'staatic_aws_auth_secret_access_key';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    protected function template(): string
    {
        return 'password';
    }

    public function label(): string
    {
        return __('Secret Access Key', 'staatic');
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        parent::render(array_merge([
            'disableAutocomplete' => \true
        ], $attributes));
    }
}
