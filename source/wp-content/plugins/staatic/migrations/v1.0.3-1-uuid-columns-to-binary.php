<?php

declare(strict_types=1);

namespace Staatic\Vendor;

use wpdb;
use Staatic\WordPress\Migrations\AbstractMigration;

return new class extends AbstractMigration {
    private $replacements = [
        'staatic_builds' => [
            'columns' => [
                'uuid' => [
                    'not_null' => \true,
                    'unique' => \true
                ],
                'parent_uuid' => []
            ]
        ],
        'staatic_crawl_queue' => [
            'columns' => [
                'uuid' => [
                    'not_null' => \true,
                    'unique' => \true
                ]
            ]
        ],
        'staatic_deployments' => [
            'columns' => [
                'uuid' => [
                    'not_null' => \true,
                    'unique' => \true
                ],
                'build_uuid' => [
                    'not_null' => \true
                ]
            ]
        ],
        'staatic_results' => [
            'columns' => [
                'uuid' => [
                    'not_null' => \true,
                    'unique' => \true
                ],
                'build_uuid' => [
                    'not_null' => \true
                ]
            ]
        ],
        'staatic_results_deployment' => [
            'columns' => [
                'result_uuid' => [
                    'not_null' => \true
                ],
                'deployment_uuid' => [
                    'not_null' => \true
                ]
            ],
            'primary_key' => ['result_uuid', 'deployment_uuid']
        ],
        'staatic_log_entries' => [
            'columns' => [
                'uuid' => [
                    'not_null' => \true,
                    'unique' => \true
                ],
                'publication_id' => [
                    'not_null' => \true
                ]
            ]
        ],
        'staatic_publications' => [
            'columns' => [
                'uuid' => [
                    'not_null' => \true,
                    'unique' => \true
                ],
                'build_uuid' => [
                    'not_null' => \true
                ],
                'deployment_uuid' => [
                    'not_null' => \true
                ]
            ]
        ]
    ];

    /**
     * @param wpdb $wpdb
     */
    public function up($wpdb): void
    {
        $this->applyReplacements($wpdb, 'binary(16)', 'UNHEX(REPLACE(%s, \'-\', \'\'))');
    }

    /**
     * @param wpdb $wpdb
     */
    public function down($wpdb): void
    {
        $this->applyReplacements($wpdb, 'varchar(40)', 'LOWER(CONCAT(
            SUBSTR(HEX(%1$s), 1, 8), \'-\',
            SUBSTR(HEX(%1$s), 9, 4), \'-\',
            SUBSTR(HEX(%1$s), 13, 4), \'-\',
            SUBSTR(HEX(%1$s), 17, 4), \'-\',
            SUBSTR(HEX(%1$s), 21)
          ))');
    }

    private function applyReplacements(wpdb $wpdb, string $columnDefinition, string $conversion): void
    {
        foreach ($this->replacements as $tableName => $tableInfo) {
            if ($tableInfo['primary_key'] ?? \false) {
                $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}{$tableName} DROP PRIMARY KEY");
            }
            foreach ($tableInfo['columns'] as $columnName => $columnInfo) {
                $currentColumnDefinition = $columnDefinition;
                // Add new uuid column
                $this->query(
                    $wpdb,
                    "ALTER TABLE {$wpdb->prefix}{$tableName} ADD {$columnName}_new {$currentColumnDefinition} AFTER {$columnName}"
                );
                // Convert column values
                $currentConversion = \sprintf($conversion, $columnName);
                $this->query(
                    $wpdb,
                    "UPDATE {$wpdb->prefix}{$tableName} SET {$columnName}_new = {$currentConversion} WHERE {$columnName} IS NOT NULL"
                );
                // Optionally add not null constraint
                if ($columnInfo['not_null'] ?? \false) {
                    $currentColumnDefinition .= ' not null';
                    $this->query(
                        $wpdb,
                        "ALTER TABLE {$wpdb->prefix}{$tableName} MODIFY {$columnName}_new {$currentColumnDefinition}"
                    );
                }
                // Optionally drop unique constraint for old column
                if ($columnInfo['unique'] ?? \false) {
                    $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}{$tableName} DROP INDEX {$columnName}");
                }
                // Drop old column
                $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}{$tableName} DROP {$columnName}");
                // Rename new column
                $this->query(
                    $wpdb,
                    "ALTER TABLE {$wpdb->prefix}{$tableName} CHANGE {$columnName}_new {$columnName} {$currentColumnDefinition}"
                );
                // Optionally add unique constraint for new column
                if ($columnInfo['unique'] ?? \false) {
                    $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}{$tableName} ADD UNIQUE ({$columnName})");
                }
            }
            if ($tableInfo['primary_key'] ?? \false) {
                $this->query(
                    $wpdb,
                    "ALTER TABLE {$wpdb->prefix}{$tableName} ADD PRIMARY KEY (" . \implode(
                        ', ',
                        $tableInfo['primary_key']
                    ) . ")"
                );
            }
        }
    }
};
