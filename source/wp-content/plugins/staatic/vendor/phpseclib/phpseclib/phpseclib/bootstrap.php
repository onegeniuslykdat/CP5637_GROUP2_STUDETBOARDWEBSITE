<?php

namespace Staatic\Vendor;

use UnexpectedValueException;
if (\extension_loaded('mbstring')) {
    if (\version_compare(\PHP_VERSION, '8.0.0') < 0 && \ini_get('mbstring.func_overload') & 2) {
        throw new UnexpectedValueException('Overloading of string functions using mbstring.func_overload ' . 'is not supported by phpseclib.');
    }
}
