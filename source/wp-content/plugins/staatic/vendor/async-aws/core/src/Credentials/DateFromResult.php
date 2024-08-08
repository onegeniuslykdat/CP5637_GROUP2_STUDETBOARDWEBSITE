<?php

namespace Staatic\Vendor\AsyncAws\Core\Credentials;

use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\Core\Result;
trait DateFromResult
{
    private function getDateFromResult(Result $result): ?DateTimeImmutable
    {
        $response = $result->info()['response'];
        if (null !== $date = $response->getHeaders(\false)['date'][0] ?? null) {
            return new DateTimeImmutable($date);
        }
        return null;
    }
}
