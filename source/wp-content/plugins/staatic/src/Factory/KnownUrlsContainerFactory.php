<?php

declare(strict_types=1);

namespace Staatic\WordPress\Factory;

use Staatic\Crawler\KnownUrlsContainer\InMemoryKnownUrlsContainer;
use Staatic\Crawler\KnownUrlsContainer\KnownUrlsContainerInterface;
use Staatic\WordPress\Bridge\KnownUrlsContainer;
use wpdb;

final class KnownUrlsContainerFactory
{
    /**
     * @var wpdb
     */
    private $wpdb;

    public function __construct(wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    public function __invoke(bool $inMemory): KnownUrlsContainerInterface
    {
        // df(['inMemory' => $inMemory]);
        // if ($inMemory) {
        //     return new InMemoryKnownUrlsContainer();
        // }
        return new KnownUrlsContainer($this->wpdb);
    }
}
