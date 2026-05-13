<?php

if (!function_exists('utf8_sanitize')) {
    function utf8_sanitize($value, string $default = ''): string
    {
        if (is_null($value)) {
            return $default;
        }

        $value = (string) $value;

        if ($value === '') {
            return $value;
        }

        $clean = @iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $value);
        if ($clean !== false) {
            return $clean;
        }

        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }
}

if (!function_exists('utf8_sanitize_html')) {
    function utf8_sanitize_html(string $html): string
    {
        if ($html === '') {
            return $html;
        }

        // Strip null bytes and control characters (keep \t, \n, \r)
        // mPDF rejects control chars like \x00, \x01, \x02 as invalid UTF-8
        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $html);

        // Strip invalid UTF-8 byte sequences using iconv with IGNORE
        $html = @iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $html);
        if ($html === false) {
            $html = '';
        }

        // Final safety: remove any remaining code units outside valid Unicode range
        // This catches edge cases where iconv might have missed something
        $html = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $html);

        return $html;
    }
}
