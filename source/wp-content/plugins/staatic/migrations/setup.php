<?php

declare(strict_types=1);

namespace Staatic\Vendor;

use wpdb;
use Staatic\WordPress\Migrations\AbstractMigration;

return new class extends AbstractMigration {
    /**
     * @param wpdb $wpdb
     */
    public function up($wpdb): void
    {
        // Tables
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_builds (\n                id bigint(20) unsigned NOT NULL auto_increment,\n                uuid binary(16) NOT NULL,\n                entry_url varchar(2083) NOT NULL,\n                destination_url varchar(2083) NOT NULL,\n                parent_uuid binary(16),\n                date_created datetime NOT NULL,\n                date_crawl_started datetime,\n                date_crawl_finished datetime,\n                num_urls_crawlable int(11),\n                num_urls_crawled int(11),\n                PRIMARY KEY  (id),\n                UNIQUE KEY uuid (uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_crawl_queue (\n                id bigint(20) unsigned NOT NULL auto_increment,\n                uuid binary(16) NOT NULL,\n                url varchar(2083) NOT NULL,\n                origin_url varchar(2083) NOT NULL,\n                transformed_url varchar(2083) NOT NULL,\n                normalized_url varchar(2083) NOT NULL,\n                found_on_url varchar(2083),\n                depth_level smallint(5) unsigned NOT NULL,\n                redirect_level smallint(5) unsigned NOT NULL,\n                tags varchar(255) NOT NULL,\n                priority smallint(5) unsigned NOT NULL,\n                PRIMARY KEY  (id),\n                UNIQUE KEY uuid (uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_deployments (\n                id bigint(20) unsigned NOT NULL auto_increment,\n                uuid binary(16) NOT NULL,\n                build_uuid binary(16) NOT NULL,\n                date_created datetime NOT NULL,\n                date_started datetime,\n                date_finished datetime,\n                num_results_total int(11),\n                num_results_deployable int(11),\n                num_results_deployed int(11),\n                metadata mediumtext,\n                PRIMARY KEY  (id),\n                UNIQUE KEY uuid (uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_known_urls (\n                hash varchar(32) NOT NULL,\n                PRIMARY KEY  (hash)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_log_entries (\n                id bigint(20) unsigned NOT NULL auto_increment,\n                uuid binary(16) NOT NULL,\n                log_date datetime NOT NULL,\n                log_level varchar(40) NOT NULL,\n                message text NOT NULL,\n                context text,\n                publication_uuid binary(16),\n                PRIMARY KEY  (id),\n                UNIQUE KEY uuid (uuid),\n                KEY publication_uuid (publication_uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_publications (\n                id bigint(20) unsigned NOT NULL auto_increment,\n                uuid binary(16) NOT NULL,\n                date_created datetime NOT NULL,\n                build_uuid binary(16) NOT NULL,\n                deployment_uuid binary(16) NOT NULL,\n                is_preview tinyint(1) UNSIGNED NOT NULL DEFAULT 0,\n                user_id bigint(20) unsigned,\n                metadata mediumtext NOT NULL,\n                status varchar(40) NOT NULL,\n                date_finished datetime,\n                current_task varchar(255),\n                PRIMARY KEY  (id),\n                UNIQUE KEY uuid (uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_results (\n                id bigint(20) unsigned NOT NULL auto_increment,\n                uuid binary(16) NOT NULL,\n                build_uuid binary(16) NOT NULL,\n                url varchar(2083) NOT NULL,\n                url_hash varchar(32) NOT NULL,\n                status_code smallint(3) NOT NULL,\n                md5 varchar(32),\n                sha1 varchar(40),\n                size int(11),\n                mime_type tinytext,\n                charset tinytext,\n                redirect_url varchar(2083),\n                original_url varchar(2083),\n                original_found_on_url varchar(2083),\n                date_created datetime NOT NULL,\n                PRIMARY KEY  (id),\n                UNIQUE KEY uuid (uuid),\n                KEY build_uuid (build_uuid),\n                KEY sha1 (sha1)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_results_deployment (\n                result_uuid binary(16) NOT NULL,\n                deployment_uuid binary(16) NOT NULL,\n                date_created datetime NOT NULL,\n                date_deployed datetime,\n                PRIMARY KEY  (result_uuid, deployment_uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        // Capabilities
        $administrator = \get_role('administrator');
        $administrator->add_cap('staatic_manage_settings', \true);
        $administrator->add_cap('staatic_publish_subset', \true);
        $administrator->add_cap('staatic_publish', \true);
        $editor = \get_role('editor');
        $editor->add_cap('staatic_publish', \true);
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        // Capabilities
        if ($administrator = \get_role('administrator')) {
            $administrator->remove_cap('staatic_manage_settings');
            $administrator->remove_cap('staatic_publish_subset');
            $administrator->remove_cap('staatic_publish');
        }
        if ($editor = \get_role('editor')) {
            $editor->remove_cap('staatic_publish');
        }
        // Tables
        $this->query($wpdb, "DROP TABLE IF EXISTS {$wpdb->prefix}staatic_builds");
        $this->query($wpdb, "DROP TABLE IF EXISTS {$wpdb->prefix}staatic_crawl_queue");
        $this->query($wpdb, "DROP TABLE IF EXISTS {$wpdb->prefix}staatic_deployments");
        $this->query($wpdb, "DROP TABLE IF EXISTS {$wpdb->prefix}staatic_known_urls");
        $this->query($wpdb, "DROP TABLE IF EXISTS {$wpdb->prefix}staatic_results");
        $this->query($wpdb, "DROP TABLE IF EXISTS {$wpdb->prefix}staatic_results_deployment");
        $this->query($wpdb, "DROP TABLE IF EXISTS {$wpdb->prefix}staatic_log_entries");
        $this->query($wpdb, "DROP TABLE IF EXISTS {$wpdb->prefix}staatic_publications");
        // Options
        $this->query($wpdb, "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'staatic_%_publication_id'");
    }
};
