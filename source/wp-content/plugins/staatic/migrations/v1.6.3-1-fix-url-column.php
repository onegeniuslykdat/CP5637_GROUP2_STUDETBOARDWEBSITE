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
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_crawl_queue MODIFY url VARCHAR(2083)");
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
    }
};
