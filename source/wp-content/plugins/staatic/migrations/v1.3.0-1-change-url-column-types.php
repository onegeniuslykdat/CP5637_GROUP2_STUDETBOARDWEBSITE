<?php

declare(strict_types=1);

namespace Staatic\Vendor;

use wpdb;
use Staatic\WordPress\Migrations\AbstractMigration;

return new class extends AbstractMigration {
    private $replacements = [
        'staatic_builds' => ['entry_url', 'destination_url'],
        'staatic_crawl_queue' => ['url', 'origin_url', 'transformed_url', 'found_on_url'],
        'staatic_known_urls' => ['url'],
        'staatic_results' => ['url', 'redirect_url', 'original_url', 'original_found_on_url']
    ];

    /**
     * @param wpdb $wpdb
     */
    public function up($wpdb): void
    {
        foreach ($this->replacements as $tableName => $columns) {
            foreach ($columns as $columnName) {
                $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}{$tableName} MODIFY {$columnName} VARCHAR(2083)");
            }
        }
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        foreach ($this->replacements as $tableName => $columns) {
            foreach ($columns as $columnName) {
                $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}{$tableName} MODIFY {$columnName} VARCHAR(255)");
            }
        }
    }
};
