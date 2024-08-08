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
            "ALTER TABLE {$wpdb->prefix}staatic_results ADD url_hash VARCHAR(32) NOT NULL DEFAULT '' AFTER url"
        );
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_results ALTER url_hash DROP DEFAULT");
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_crawl_queue DROP url_hash");
    }
};
