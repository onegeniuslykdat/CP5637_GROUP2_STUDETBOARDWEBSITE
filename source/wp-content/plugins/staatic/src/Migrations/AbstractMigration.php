<?php

declare(strict_types=1);

namespace Staatic\WordPress\Migrations;

use RuntimeException;
use wpdb;

abstract class AbstractMigration implements MigrationInterface
{
    /**
     * @param wpdb $wpdb
     * @param string $query
     */
    protected function query($wpdb, $query)
    {
        $result = $wpdb->query($query);
        if ($result === \false) {
            throw new RuntimeException("Unable to execute query: '{$query}': {$wpdb->last_error}");
        }

        return $result;
    }

    /**
     * @param string $oldName
     * @param string $newName
     */
    protected function renameOption($oldName, $newName): void
    {
        $value = get_option($oldName);
        if ($value === \false) {
            return;
        }
        update_option($newName, $value);
        delete_option($oldName);
    }
}
