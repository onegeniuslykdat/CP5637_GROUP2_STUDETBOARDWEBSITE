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
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_known_urls DROP url");
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_known_urls DROP id");
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_known_urls DROP INDEX hash");
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_known_urls ADD PRIMARY KEY(hash)");
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_known_urls DROP PRIMARY KEY");
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_known_urls ADD INDEX hash (hash)");
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_known_urls ADD id MEDIUMINT(8) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (id)"
        );
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_known_urls ADD url VARCHAR(2083) NULL AFTER hash");
    }
};
