<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

final class Rfc7230
{
    public const HEADER_REGEX = "(^([^()<>@,;:\\\"/[\\]?={}\x01- ]++):[ \t]*+((?:[ \t]*+[!-~\x80-\xff]++)*+)[ \t]*+\r?\n)m";
    public const HEADER_FOLD_REGEX = "(\r?\n[ \t]++)";
}
