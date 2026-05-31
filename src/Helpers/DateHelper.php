<?php
namespace App\Helpers;

class DateHelper
{
    public static function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    public static function today(): string
    {
        return date('Y-m-d');
    }

    public static function format(string $date, string $format = 'd/m/Y'): string
    {
        $ts = strtotime($date);
        return $ts ? date($format, $ts) : '';
    }

    public static function formatTime(string $time): string
    {
        if ($time === '') return '';
        return substr($time, 0, 5);
    }

    public static function isExpired(string $datetime): bool
    {
        return strtotime($datetime) < time();
    }

    public static function diffHours(string $start, string $end): float
    {
        return (strtotime($end) - strtotime($start)) / 3600;
    }
}
