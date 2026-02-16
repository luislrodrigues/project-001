<?php

namespace App\Support;

class ReportNumberConverter
{
    /** Prefix for the report number (e.g. INF = Informe) */
    protected static string $prefix = 'INF';

    /** Minimum length for the numeric part (zero-padded) */
    protected static int $padding = 5;

    /**
     * Generate consecutive-style number from report id.
     * Example: id 1 => "INF-00001", id 42 => "INF-00042"
     */
    public static function fromId(int $id): string
    {
        $numeric = str_pad((string) $id, static::$padding, '0', STR_PAD_LEFT);
        return static::$prefix . '-' . $numeric;
    }

    /**
     * Optional: include year in nomenclatura.
     * Example: id 1, year 2026 => "INF-2026-00001"
     */
    public static function fromIdAndYear(int $id, ?int $year = null): string
    {
        $year = $year ?? (int) date('Y');
        $numeric = str_pad((string) $id, static::$padding, '0', STR_PAD_LEFT);
        return static::$prefix . '-' . $year . '-' . $numeric;
    }

    public static function setPrefix(string $prefix): void
    {
        static::$prefix = $prefix;
    }

    public static function setPadding(int $padding): void
    {
        static::$padding = $padding;
    }

    /**
     * Parse consecutivo string (e.g. "INF-00042", "42", "00042") to report id.
     * Returns null if the value does not match a valid number.
     */
    public static function parseToId(string $value): ?int
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        // Allow "INF-00042" or "INF-42" format
        if (stripos($value, static::$prefix . '-') === 0) {
            $value = substr($value, strlen(static::$prefix . '-'));
        }
        $value = ltrim($value, '0');
        if ($value === '') {
            return 0; // "00000" or "0" => id 0 (no report, but valid parse)
        }
        if (!ctype_digit($value)) {
            return null;
        }
        $id = (int) $value;
        return $id >= 0 ? $id : null;
    }
}
