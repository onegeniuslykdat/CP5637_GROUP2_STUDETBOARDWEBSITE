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
            "ALTER TABLE {$wpdb->prefix}staatic_crawl_queue ADD normalized_url VARCHAR(2083) NOT NULL DEFAULT '' AFTER transformed_url"
        );
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_crawl_queue ALTER normalized_url DROP DEFAULT");
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_crawl_queue DROP normalized_url");
    }
};
