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
            "ALTER TABLE {$wpdb->prefix}staatic_results CHANGE resource_uuid resource_id varchar(40) not null"
        );
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_results CHANGE resource_id resource_uuid varchar(40) not null"
        );
    }
};
