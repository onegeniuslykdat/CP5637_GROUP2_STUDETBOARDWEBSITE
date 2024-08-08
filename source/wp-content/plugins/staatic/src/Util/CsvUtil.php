<?php

declare(strict_types=1);

namespace Staatic\WordPress\Util;

final class CsvUtil
{
    public static function strPutCsv(array $input, string $delimiter = ',', string $enclosure = '"')
    {
        $fp = fopen('php://temp', 'r+');
        fputcsv($fp, $input, $delimiter, $enclosure);
        rewind($fp);
        $data = fgets($fp);
        fclose($fp);

        return rtrim($data);
    }
}
