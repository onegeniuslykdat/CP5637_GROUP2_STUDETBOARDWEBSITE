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
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_results ADD INDEX (build_uuid)");
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_log_entries ADD INDEX (publication_uuid)");
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_log_entries DROP INDEX publication_uuid");
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_results DROP INDEX build_uuid");
    }
};
