<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Validator;

interface ValidatorInterface
{
    public function getPattern(): string;
    /**
     * @param string $uuid
     */
    public function validate($uuid): bool;
}
