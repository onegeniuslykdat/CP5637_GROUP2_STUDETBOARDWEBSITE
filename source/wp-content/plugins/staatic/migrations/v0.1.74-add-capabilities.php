<?php

declare(strict_types=1);

namespace Staatic\Vendor;

use wpdb;
use Staatic\WordPress\Migrations\MigrationInterface;

return new class implements MigrationInterface {
    /**
     * @param wpdb $wpdb
     */
    public function up($wpdb): void
    {
        $role = \get_role('administrator');
        $role->add_cap('staatic_manage_settings', \true);
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $role = \get_role('administrator');
        $role->remove_cap('staatic_manage_settings');
    }
};
