<?php

namespace Staatic\Framework\Logger;

use Staatic\Vendor\Psr\Log\LoggerTrait as PsrLoggerTrait;
trait LoggerTrait
{
    use PsrLoggerTrait;
    private function getSourceContext(): array
    {
        $backtrace = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($backtrace as $index => $item) {
            if (substr_compare($item['class'], 'Logger', -strlen('Logger')) === 0 || strpos($item['class'], '\LoggerProxy') !== false) {
                continue;
            }
            $prevItem = $backtrace[$index - 1] ?? null;
            return ['sourceFile' => $prevItem['file'] ?? $item['file'] ?? null, 'sourceLine' => $prevItem['line'] ?? $item['line'] ?? null, 'sourceClass' => $item['class'], 'sourceFunction' => $item['function']];
        }
        return [];
    }
    private function getShortClassName(string $className): string
    {
        $classNameParts = explode('\\', $className);
        return array_pop($classNameParts);
    }
}
