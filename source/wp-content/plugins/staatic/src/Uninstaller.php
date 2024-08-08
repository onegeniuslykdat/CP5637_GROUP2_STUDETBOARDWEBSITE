<?php

declare(strict_types=1);

namespace Staatic\WordPress;

use wpdb;

final class Uninstaller
{
    /**
     * @var wpdb
     */
    private $wpdb;

    public function __construct(wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    public function uninstall(): void
    {
        if (get_option('staatic_uninstall_data', \true)) {
            $this->removeData();
        }
        if (get_option('staatic_uninstall_settings', \true)) {
            $this->removeSettings();
            $this->removeOptions();
        }
    }

    private function removeData(): void
    {
        // var_dump('remove data'); return; //!
        require_once __DIR__ . '/Migrations/MigrationInterface.php';
        require_once __DIR__ . '/Migrations/AbstractMigration.php';
        $migration = require __DIR__ . '/../migrations/setup.php';
        $migration->down($this->wpdb);
        delete_option('staatic_database_version');
    }

    private function removeSettings(): void
    {
        $settings = [
            'staatic_background_process_timeout',
            'staatic_crawler_dom_parser',
            'staatic_crawler_lowercase_urls',
            'staatic_crawler_process_not_found',
            'staatic_http_auth_password',
            'staatic_http_auth_username',
            'staatic_http_concurrency',
            'staatic_http_delay',
            'staatic_http_timeout',
            'staatic_http_https_to_http',
            'staatic_logging_level',
            'staatic_override_site_url',
            'staatic_page_not_found_path',
            'staatic_ssl_verify_behavior',
            'staatic_ssl_verify_path',
            'staatic_uninstall_data',
            'staatic_uninstall_settings',
            'staatic_work_directory',
            'staatic_additional_paths',
            'staatic_additional_redirects',
            'staatic_additional_urls',
            'staatic_destination_url',
            'staatic_preview_url',
            'staatic_exclude_urls',
            'staatic_deployment_method',
            'staatic_aws_auth_access_key_id',
            'staatic_aws_auth_profile',
            'staatic_aws_auth_secret_access_key',
            'staatic_aws_cloudfront_distribution_id',
            'staatic_aws_cloudfront_invalidate_everything_path',
            'staatic_aws_cloudfront_max_invalidation_paths',
            'staatic_aws_endpoint',
            'staatic_aws_s3_object_acl',
            'staatic_aws_region',
            'staatic_aws_retain_paths',
            'staatic_aws_s3_bucket',
            'staatic_aws_s3_prefix',
            'staatic_github_branch',
            'staatic_github_commit_message',
            'staatic_github_prefix',
            'staatic_github_repository',
            'staatic_github_retain_paths',
            'staatic_github_token',
            'staatic_filesystem_apache_configs',
            'staatic_filesystem_retain_paths',
            'staatic_filesystem_nginx_configs',
            'staatic_filesystem_symlink_uploads',
            'staatic_filesystem_target_directory',
            'staatic_netlify_access_token',
            'staatic_netlify_site_id',
            'staatic_sftp_host',
            'staatic_sftp_password',
            'staatic_sftp_port',
            'staatic_sftp_ssh_key',
            'staatic_sftp_ssh_key_password',
            'staatic_sftp_target_directory',
            'staatic_sftp_timeout',
            'staatic_sftp_username'
        ];
        foreach ($settings as $setting) {
            // var_dump($setting); continue; //!
            delete_option($setting);
        }
    }

    private function removeOptions(): void
    {
        $options = [
            'staatic_current_publication_id',
            'staatic_latest_publication_id',
            'staatic_active_publication_id',
            'staatic_active_preview_publication_id',
            'staatic_test_request_status'
        ];
        foreach ($options as $option) {
            // var_dump($option); continue; //!
            delete_option($option);
        }
    }
}
