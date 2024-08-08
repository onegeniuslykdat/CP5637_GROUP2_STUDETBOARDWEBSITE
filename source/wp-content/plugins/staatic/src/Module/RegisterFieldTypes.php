<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

use wpdb;

final class RegisterFieldTypes implements ModuleInterface
{
    /**
     * @var wpdb
     */
    private $wpdb;

    public function __construct(wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    public function hooks(): void
    {
        add_action('init', [$this, 'registerFieldTypes']);
    }

    public function registerFieldTypes(): void
    {
        $uuidFields = ['uuid', 'build_uuid', 'deployment_uuid', 'parent_uuid', 'publication_uuid'];
        foreach ($uuidFields as $field) {
            $this->wpdb->field_types[$field] = "UNHEX(REPLACE('%s', '-', ''))";
        }
    }

    public static function getDefaultPriority(): int
    {
        return 100;
    }
}
