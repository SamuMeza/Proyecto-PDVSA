<?php
namespace App\Helpers;

class StringHelper
{
    public static function truncate(string $text, int $maxLength = 100, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $maxLength) return $text;
        return mb_substr($text, 0, $maxLength - mb_strlen($suffix)) . $suffix;
    }

    public static function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\w\s-]/', '', $text);
        $text = preg_replace('/[\s_]+/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }

    public static function randomString(int $length = 16): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    public static function normalizeSpaces(string $text): string
    {
        return preg_replace('/\s+/', ' ', trim($text));
    }

    public static function contains(string $haystack, string $needle): bool
    {
        return mb_strpos($haystack, $needle) !== false;
    }

    public static function startsWith(string $haystack, string $needle): bool
    {
        return mb_substr($haystack, 0, mb_strlen($needle)) === $needle;
    }

    public static function endsWith(string $haystack, string $needle): bool
    {
        $length = mb_strlen($needle);
        if ($length === 0) return true;
        return mb_substr($haystack, -$length) === $needle;
    }
}
