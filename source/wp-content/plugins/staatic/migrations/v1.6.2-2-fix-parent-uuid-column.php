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
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_builds MODIFY parent_uuid BINARY(16)");
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
    }
};
