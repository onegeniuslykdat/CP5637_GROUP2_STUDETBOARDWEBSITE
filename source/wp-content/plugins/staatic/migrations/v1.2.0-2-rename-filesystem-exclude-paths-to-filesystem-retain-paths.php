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
        $this->renameOption('staatic_filesystem_exclude_paths', 'staatic_filesystem_retain_paths');
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $this->renameOption('staatic_filesystem_retain_paths', 'staatic_filesystem_exclude_paths');
    }
};
