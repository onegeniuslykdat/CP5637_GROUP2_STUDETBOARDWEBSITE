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
            "\n            DELETE rd\n            FROM {$wpdb->prefix}staatic_results_deployment AS rd\n                LEFT JOIN {$wpdb->prefix}staatic_deployments AS d ON d.uuid = rd.deployment_uuid\n            WHERE d.uuid IS NULL\n        "
        );
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        // Nothing to do here.
    }
};
