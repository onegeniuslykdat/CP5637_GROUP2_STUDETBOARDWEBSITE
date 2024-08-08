<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use RuntimeException;

final class PartialRenderer
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var mixed[]
     */
    private $paths = [];

    /**
     * @var mixed[]
     */
    private $vars = [];

    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    public function addPath(string $path): void
    {
        $this->addPaths([$path]);
    }

    public function addPaths(array $paths): void
    {
        $this->paths = array_merge($this->paths, $paths);
    }

    public function setPaths(array $paths): void
    {
        $this->paths = $paths;
    }

    public function addVars(array $vars): void
    {
        $this->vars = array_merge($this->vars, $vars);
    }

    public function render(string $path, array $vars = []): void
    {
        $vars = array_merge([
            '_request' => [
                'isAjax' => wp_doing_ajax()
            ],
            '_formatter' => $this->formatter
        ], $this->vars, $vars);
        extract($vars);
        $templateFile = $this->getTemplate($path);
        if (!is_readable($templateFile)) {
            throw new RuntimeException("Unable to load partial in {$resolvedPath}");
        }
        require $templateFile;
    }

    private function getTemplate(string $path): string
    {
        $template = 'staatic/' . $path;
        if (!$resolvedPath = locate_template($template)) {
            $resolvedPath = $this->resolvePath($path);
        }

        return apply_filters('staatic_get_template', $resolvedPath, $path);
    }

    private function resolvePath(string $path): string
    {
        if (strncmp($path, '/', strlen('/')) === 0) {
            return $path;
        }
        foreach ($this->paths as $basePath) {
            $resolvedPath = sprintf('%s/%s', $basePath, $path);
            if (file_exists($resolvedPath)) {
                return $resolvedPath;
            }
        }
        $lookupPaths = implode(', ', $this->paths);

        throw new RuntimeException("Unable to resolve path {$path} (looked in: {$lookupPaths})");
    }

    public function return(string $path, array $vars = []): string
    {
        ob_start();
        $this->render($path, $vars);

        return ob_get_clean();
    }
}
