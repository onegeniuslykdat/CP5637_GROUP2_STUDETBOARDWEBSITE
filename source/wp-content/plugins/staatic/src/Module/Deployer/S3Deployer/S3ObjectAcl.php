<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class S3ObjectAcl extends AbstractSetting
{
    public function name(): string
    {
        return 'staatic_aws_s3_object_acl';
    }

    public function type(): string
    {
        return self::TYPE_STRING;
    }

    public function label(): string
    {
        return __('Object ACL', 'staatic');
    }

    public function description(): string
    {
        return sprintf(
            /* translators: 1: Link to AWS documentation. */
            __('Optionally apply the specified (canned) ACL to uploaded files.<br>For more information, see <a href="%1$s" target="_blank" rel="noopener">Canned ACL documentation</a>.', 'staatic'),
            'https://docs.aws.amazon.com/AmazonS3/latest/userguide/acl-overview.html#canned-acl'
        );
    }

    /**
     * @param mixed[] $attributes
     */
    public function render($attributes = []): void
    {
        parent::render(array_merge([
            'options' => $this->options()
        ], $attributes));
    }

    private function options(): array
    {
        $options = [
            'private',
            'public-read',
            'public-read-write',
            'aws-exec-read',
            'authenticated-read',
            'bucket-owner-read',
            'bucket-owner-full-control',
            'log-delivery-write'
        ];

        return array_combine($options, $options);
    }
}
