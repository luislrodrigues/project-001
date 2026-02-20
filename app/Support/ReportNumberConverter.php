<?php

namespace App\Support;

class ReportNumberConverter
{
    /** Prefix for the report number (e.g. SS) */
    protected static string $prefix = 'SS';

    /** Minimum length for the numeric part (zero-padded) */
    protected static int $padding = 4;

    /**
     * Generate consecutive-style number from report id.
     * Example: id 1 => "No: SS-0001", id 42 => "No: SS-0042"
     */
    public static function fromId(int $id): string
    {
        $numeric = str_pad((string) $id, static::$padding, '0', STR_PAD_LEFT);
        return 'No: ' . static::$prefix . '-' . $numeric;
    }

    /**
     * Optional: include year in nomenclatura.
     * Example: id 1, year 2026 => "No: SS-2026-0001"
     */
    public static function fromIdAndYear(int $id, ?int $year = null): string
    {
        $year = $year ?? (int) date('Y');
        $numeric = str_pad((string) $id, static::$padding, '0', STR_PAD_LEFT);
        return 'No: ' . static::$prefix . '-' . $year . '-' . $numeric;
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
     * Parse consecutivo string (e.g. "No: SS-0001", "SS-0001", "0001", "1") to report id.
     * Returns null if the value does not match a valid number.
     */
    public static function parseToId(string $value): ?int
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        // Strip optional "No:" label
        if (stripos($value, 'No:') === 0) {
            $value = trim(substr($value, 3));
        }
        // Allow "SS-0001" or "SS-1" format (case-insensitive)
        if (stripos($value, static::$prefix . '-') === 0) {
            $value = substr($value, strlen(static::$prefix . '-'));
        }
        // If includes year (e.g., "2026-0001"), drop the year part
        if (preg_match('/^\d{4}-/', $value) === 1) {
            $value = preg_replace('/^\d{4}-/', '', $value);
        }
        $value = ltrim($value, '0');
        if ($value === '') {
            return 0;
        }
        if (!ctype_digit($value)) {
            return null;
        }
        $id = (int) $value;
        return $id >= 0 ? $id : null;
    }
}
