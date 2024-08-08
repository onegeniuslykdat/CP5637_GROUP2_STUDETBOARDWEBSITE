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
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_results_deployment DROP PRIMARY KEY");
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_results_deployment ADD PRIMARY KEY(deployment_uuid, result_uuid)"
        );
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_results_deployment DROP PRIMARY KEY");
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_results_deployment ADD PRIMARY KEY(result_uuid, deployment_uuid)"
        );
    }
};
