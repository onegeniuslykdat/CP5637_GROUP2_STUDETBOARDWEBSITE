<?php

declare(strict_types=1);

namespace Staatic\WordPress\Factory;

use Staatic\WordPress\Service\Formatter;
use Staatic\WordPress\Service\PartialRenderer;

final class PartialRendererFactory
{
    /**
     * @var Formatter
     */
    private $formatter;

    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function __invoke(): PartialRenderer
    {
        $partialRenderer = new PartialRenderer($this->formatter);
        $partialRenderer->addPath(__DIR__ . '/../../partials');

        return $partialRenderer;
    }
}
