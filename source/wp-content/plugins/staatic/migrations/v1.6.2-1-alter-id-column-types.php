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
            "ALTER TABLE {$wpdb->prefix}staatic_builds CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_crawl_queue CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_deployments CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_log_entries CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_publications CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_publications CHANGE `user_id` `user_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_results CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_builds CHANGE `id` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_crawl_queue CHANGE `id` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_deployments CHANGE `id` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_log_entries CHANGE `id` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_publications CHANGE `id` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_publications CHANGE `user_id` `user_id` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL"
        );
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_results CHANGE `id` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT"
        );
    }
};
