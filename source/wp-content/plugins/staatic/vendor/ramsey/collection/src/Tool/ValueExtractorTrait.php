<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Tool;

use Staatic\Vendor\Ramsey\Collection\Exception\InvalidPropertyOrMethod;
use Staatic\Vendor\Ramsey\Collection\Exception\UnsupportedOperationException;
use function is_array;
use function is_object;
use function method_exists;
use function property_exists;
use function sprintf;
trait ValueExtractorTrait
{
    /**
     * @param mixed $element
     * @param string|null $propertyOrMethod
     * @return mixed
     */
    protected function extractValue($element, $propertyOrMethod)
    {
        if ($propertyOrMethod === null) {
            return $element;
        }
        if (!is_object($element) && !is_array($element)) {
            throw new UnsupportedOperationException(sprintf('The collection type "%s" does not support the $propertyOrMethod parameter', $this->getType()));
        }
        if (is_array($element)) {
            if (!isset($element[$propertyOrMethod])) {
                throw new InvalidPropertyOrMethod(sprintf('Key or index "%s" not found in collection elements', $propertyOrMethod));
            }
            return $element[$propertyOrMethod];
        }
        if (property_exists($element, $propertyOrMethod)) {
            return $element->{$propertyOrMethod};
        }
        if (method_exists($element, $propertyOrMethod)) {
            return $element->{$propertyOrMethod}();
        }
        throw new InvalidPropertyOrMethod(sprintf('Method or property "%s" not defined in %s', $propertyOrMethod, get_class($element)));
    }
}
