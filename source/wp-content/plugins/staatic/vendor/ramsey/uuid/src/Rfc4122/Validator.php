<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Rfc4122;

use Staatic\Vendor\Ramsey\Uuid\Uuid;
use Staatic\Vendor\Ramsey\Uuid\Validator\ValidatorInterface;
use function preg_match;
use function str_replace;
final class Validator implements ValidatorInterface
{
    private const VALID_PATTERN = '\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-' . '[1-8][0-9A-Fa-f]{3}-[ABab89][0-9A-Fa-f]{3}-[0-9A-Fa-f]{12}\z';
    public function getPattern(): string
    {
        return self::VALID_PATTERN;
    }
    /**
     * @param string $uuid
     */
    public function validate($uuid): bool
    {
        $uuid = str_replace(['urn:', 'uuid:', 'URN:', 'UUID:', '{', '}'], '', $uuid);
        $uuid = strtolower($uuid);
        return $uuid === Uuid::NIL || $uuid === Uuid::MAX || preg_match('/' . self::VALID_PATTERN . '/Dms', $uuid);
    }
}
