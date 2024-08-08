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
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_builds (\n                id mediumint(8) unsigned NOT NULL auto_increment,\n                uuid varchar(40) NOT NULL,\n                entry_url varchar(255) NOT NULL,\n                destination_url varchar(255) NOT NULL,\n                parent_uuid varchar(40),\n                date_created datetime NOT NULL,\n                date_crawl_started datetime,\n                date_crawl_finished datetime,\n                num_urls_crawlable int(11),\n                num_urls_crawled int(11),\n                PRIMARY KEY  (id),\n                UNIQUE KEY uuid (uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_crawl_queue (\n                id mediumint(8) unsigned NOT NULL auto_increment,\n                uuid varchar(40) NOT NULL,\n                url varchar(255) NOT NULL,\n                origin_url varchar(255) NOT NULL,\n                transformed_url varchar(255) NOT NULL,\n                found_on_url varchar(255),\n                depth_level smallint(5) unsigned NOT NULL,\n                redirect_level smallint(5) unsigned NOT NULL,\n                tags varchar(255) NOT NULL,\n                priority smallint(5) unsigned NOT NULL,\n                PRIMARY KEY  (id),\n                UNIQUE KEY uuid (uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_deployments (\n                id mediumint(8) unsigned NOT NULL auto_increment,\n                uuid varchar(40) NOT NULL,\n                build_uuid varchar(40) NOT NULL,\n                date_created datetime NOT NULL,\n                date_started datetime,\n                date_finished datetime,\n                num_results_total int(11),\n                num_results_deployable int(11),\n                num_results_deployed int(11),\n                metadata mediumtext,\n                PRIMARY KEY  (id),\n                UNIQUE KEY uuid (uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_known_urls (\n                id mediumint(8) unsigned NOT NULL auto_increment,\n                hash varchar(32) NOT NULL,\n                url varchar(255) NOT NULL,\n                PRIMARY KEY  (id),\n                UNIQUE KEY hash (hash)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_results (\n                id mediumint(8) unsigned NOT NULL auto_increment,\n                uuid varchar(40) NOT NULL,\n                build_uuid varchar(40) NOT NULL,\n                url varchar(255) NOT NULL,\n                status_code smallint(3) NOT NULL,\n                resource_uuid varchar(40) NOT NULL,\n                md5 varchar(32),\n                sha1 varchar(40),\n                size int(11),\n                mime_type tinytext,\n                charset tinytext,\n                redirect_url varchar(255),\n                original_url varchar(255),\n                original_found_on_url varchar(255),\n                date_created datetime NOT NULL,\n                PRIMARY KEY  (id),\n                UNIQUE KEY uuid (uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_results_deployment (\n                result_uuid varchar(40) NOT NULL,\n                deployment_uuid varchar(40) NOT NULL,\n                date_created datetime NOT NULL,\n                date_deployed datetime,\n                PRIMARY KEY  (result_uuid, deployment_uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_log_entries (\n                id mediumint(8) unsigned NOT NULL auto_increment,\n                uuid varchar(40) NOT NULL,\n                log_date datetime NOT NULL,\n                log_level varchar(40) NOT NULL,\n                message text NOT NULL,\n                context text,\n                publication_id varchar(40),\n                PRIMARY KEY  (id),\n                KEY uuid (uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
        $this->query(
            $wpdb,
            "\n            CREATE TABLE {$wpdb->prefix}staatic_publications (\n                id mediumint(8) unsigned NOT NULL auto_increment,\n                uuid varchar(40) NOT NULL,\n                date_created datetime NOT NULL,\n                build_uuid varchar(40) NOT NULL,\n                deployment_uuid varchar(40) NOT NULL,\n                user_id mediumint(8) unsigned,\n                metadata mediumtext NOT NULL,\n                status varchar(40) NOT NULL,\n                date_finished datetime,\n                current_task varchar(255),\n                PRIMARY KEY  (id),\n                UNIQUE KEY uuid (uuid)\n            ) {$wpdb->get_charset_collate()};\n        "
        );
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $this->query($wpdb, "DROP TABLE {$wpdb->prefix}staatic_builds");
        $this->query($wpdb, "DROP TABLE {$wpdb->prefix}staatic_crawl_queue");
        $this->query($wpdb, "DROP TABLE {$wpdb->prefix}staatic_deployments");
        $this->query($wpdb, "DROP TABLE {$wpdb->prefix}staatic_known_urls");
        $this->query($wpdb, "DROP TABLE {$wpdb->prefix}staatic_results");
        $this->query($wpdb, "DROP TABLE {$wpdb->prefix}staatic_results_deployment");
        $this->query($wpdb, "DROP TABLE {$wpdb->prefix}staatic_log_entries");
        $this->query($wpdb, "DROP TABLE {$wpdb->prefix}staatic_publications");
    }
};
