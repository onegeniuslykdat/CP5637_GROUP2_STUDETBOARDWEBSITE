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
        $administrator = \get_role('administrator');
        $administrator->add_cap('staatic_publish_subset', \true);
        $administrator->add_cap('staatic_publish', \true);
        $editor = \get_role('editor');
        $editor->add_cap('staatic_publish', \true);
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $editor = \get_role('editor');
        $editor->remove_cap('staatic_publish');
        $administrator = \get_role('administrator');
        $administrator->remove_cap('staatic_publish');
        $administrator->remove_cap('staatic_publish_subset');
    }
};
