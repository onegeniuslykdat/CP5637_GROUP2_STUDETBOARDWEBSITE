<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable;

use RuntimeException;

class ValueAccessor
{
    /**
     * @param string $key
     * @param string|null $getter
     */
    public static function getValueByKeyRecursive($item, $key, $getter = null)
    {
        $name = explode('.', $key);
        while ($key = array_shift($name)) {
            $value = self::getValueByKey($item, $key, $getter);
            if ($value === null) {
                return null;
            }
            $item = $value;
        }

        return $value;
    }

    /**
     * @param string $key
     * @param string|null $getter
     */
    public static function getValueByKey($item, $key, $getter = null)
    {
        $value = \false;
        if (is_array($item) && isset($item[$key])) {
            $value = $item[$key];
        } elseif (is_object($item)) {
            if ($getter !== null) {
                $value = $item->{$getter}();
            } else {
                $camelcaseKey = str_replace('_', '', $key);
                if (method_exists($item, $camelcaseKey)) {
                    $value = $item->{$camelcaseKey}();
                } elseif (method_exists($item, 'get' . $camelcaseKey)) {
                    $value = $item->{'get' . $camelcaseKey}();
                } elseif (property_exists($item, $camelcaseKey)) {
                    $value = $item->{$camelcaseKey};
                }
            }
        }
        if ($value === \false) {
            throw new RuntimeException("Unable to get item value for '{$key}'");
        }

        return $value;
    }
}
