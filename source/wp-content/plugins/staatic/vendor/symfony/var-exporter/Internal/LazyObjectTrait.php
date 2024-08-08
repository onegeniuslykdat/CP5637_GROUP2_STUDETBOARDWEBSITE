<?php

namespace Staatic\Vendor\Symfony\Component\VarExporter\Internal;

use Staatic\Vendor\Symfony\Component\Serializer\Attribute\Ignore;
if (\PHP_VERSION_ID >= 80300) {
    trait LazyObjectTrait
    {
        /**
         * @readonly
         * @var LazyObjectState
         */
        private $lazyObjectState;
    }
} else {
    trait LazyObjectTrait
    {
        /**
         * @var LazyObjectState
         */
        private $lazyObjectState;
    }
}
