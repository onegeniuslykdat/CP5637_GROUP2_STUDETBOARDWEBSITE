<?php

namespace Staatic\Vendor\Symfony\Component\Config\Exception;

use Exception;
use Throwable;
use JsonException;
class LoaderLoadException extends Exception
{
    /**
     * @param mixed $resource
     */
    public function __construct($resource, ?string $sourceResource = null, int $code = 0, ?Throwable $previous = null, ?string $type = null)
    {
        if (!\is_string($resource)) {
            try {
                $resource = json_encode($resource, 0);
            } catch (JsonException $exception) {
                $resource = sprintf('resource of type "%s"', get_debug_type($resource));
            }
        }
        $message = '';
        if ($previous) {
            if (substr_compare($previous->getMessage(), '.', -strlen('.')) === 0) {
                $trimmedMessage = substr($previous->getMessage(), 0, -1);
                $message .= sprintf('%s', $trimmedMessage) . ' in ';
            } else {
                $message .= sprintf('%s', $previous->getMessage()) . ' in ';
            }
            $message .= $resource . ' ';
            if (null === $sourceResource) {
                $message .= sprintf('(which is loaded in resource "%s")', $resource);
            } else {
                $message .= sprintf('(which is being imported from "%s")', $sourceResource);
            }
            $message .= '.';
        } elseif (null === $sourceResource) {
            $message .= sprintf('Cannot load resource "%s".', $resource);
        } else {
            $message .= sprintf('Cannot import resource "%s" from "%s".', $resource, $sourceResource);
        }
        if ('@' === $resource[0]) {
            $parts = explode(\DIRECTORY_SEPARATOR, $resource);
            $bundle = substr($parts[0], 1);
            $message .= sprintf(' Make sure the "%s" bundle is correctly registered and loaded in the application kernel class.', $bundle);
            $message .= sprintf(' If the bundle is registered, make sure the bundle path "%s" is not empty.', $resource);
        } elseif (null !== $type) {
            $message .= sprintf(' Make sure there is a loader supporting the "%s" type.', $type);
        }
        parent::__construct($message, $code, $previous);
    }
    /**
     * @param mixed $var
     */
    protected function varToString($var)
    {
        if (\is_object($var)) {
            return sprintf('Object(%s)', get_class($var));
        }
        if (\is_array($var)) {
            $a = [];
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, $this->varToString($v));
            }
            return sprintf('Array(%s)', implode(', ', $a));
        }
        if (\is_resource($var)) {
            return sprintf('Resource(%s)', get_resource_type($var));
        }
        if (null === $var) {
            return 'null';
        }
        if (\false === $var) {
            return 'false';
        }
        if (\true === $var) {
            return 'true';
        }
        return (string) $var;
    }
}
