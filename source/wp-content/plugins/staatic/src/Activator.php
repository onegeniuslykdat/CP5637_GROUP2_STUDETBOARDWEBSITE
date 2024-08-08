<?php

declare(strict_types=1);

namespace Staatic\WordPress;

use Staatic\WordPress\Migrations\MigrationCoordinatorFactory;

final class Activator
{
    /**
     * @var MigrationCoordinatorFactory
     */
    private $coordinatorFactory;

    private const NAMESPACE = 'staatic';

    public function __construct(MigrationCoordinatorFactory $coordinatorFactory)
    {
        $this->coordinatorFactory = $coordinatorFactory;
    }

    public function activate(): void
    {
        $coordinator = ($this->coordinatorFactory)(self::NAMESPACE);
        if ($coordinator->shouldMigrate()) {
            $coordinator->migrate();
        }
    }
}
