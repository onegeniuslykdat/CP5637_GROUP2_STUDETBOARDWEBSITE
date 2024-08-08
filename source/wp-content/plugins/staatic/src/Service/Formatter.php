<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use DateTimeImmutable;
use DateTimeInterface;

final class Formatter
{
    public function identifier(string $id): string
    {
        return substr($id, strrpos($id, '-') + 1);
    }

    public function bytes(?int $bytes, $decimals = 0): string
    {
        if ($bytes === null) {
            return '-';
        }
        if ($bytes < 1024) {
            return "{$bytes} bytes";
        }
        $result = size_format($bytes, $decimals);
        $result = str_replace('&nbsp;', ' ', $result);

        return $result;
    }

    public function number($number, int $decimals = 0): string
    {
        if ($number === null) {
            return '-';
        }
        $result = number_format_i18n($number, $decimals);
        $result = str_replace('&nbsp;', ' ', $result);

        return $result;
    }

    public function date(?DateTimeInterface $date): string
    {
        if ($date === null) {
            return '-';
        }
        $localizedDate = $this->localizeDate($date);

        return sprintf(__('%1$s at %2$s'), $localizedDate->format(__('Y/m/d')), $localizedDate->format(__('g:i a')));
    }

    public function shortDate(?DateTimeInterface $date): string
    {
        if ($date === null) {
            return '-';
        }
        $timestamp = $date->getTimestamp();
        $difference = (new DateTimeImmutable())->getTimestamp() - $timestamp;
        if ($difference === 0) {
            return __('now', 'staatic');
        } elseif ($difference > 0 && $difference < \DAY_IN_SECONDS) {
            return sprintf(__('%s ago'), human_time_diff($timestamp));
        } else {
            $localizedDate = $this->localizeDate($date);

            return $localizedDate->format(__('Y/m/d'));
        }
    }

    public function difference(?DateTimeInterface $dateFrom, ?DateTimeInterface $dateTo): string
    {
        if ($dateFrom === null || $dateTo === null) {
            return '-';
        }

        return human_time_diff($dateFrom->getTimestamp(), $dateTo->getTimestamp());
    }

    private function localizeDate(DateTimeInterface $date): DateTimeInterface
    {
        return Polyfill::dateTimeFromInterface($date)->setTimezone(Polyfill::wp_timezone());
    }

    public function logMessage(string $message): string
    {
        return wp_kses(preg_replace_callback('~([\'\s])(https?://[^\'\s]+)([\'\s]|$)~', function ($match) {
            return $match[1] . sprintf('<a href="%1$s" target="_blank">%1$s</a>', esc_url($match[2])) . $match[3];
        }, $message), [
            'a' => [
                'href' => \true,
                'target' => \true
            ]
        ]);
    }
}
