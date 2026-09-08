<?php

if (!function_exists('parse_thai_time')) {
    /**
     * Parses input into a valid timestamp integer, or null if invalid/empty.
     *
     * @param mixed $time Timestamp integer, date string, or null.
     * @return int|null
     */
    function parse_thai_time($time): ?int
    {
        if (empty($time) || $time === '0000-00-00' || $time === '0000-00-00 00:00:00') {
            return null;
        }

        if (is_numeric($time)) {
            $ts = (int)$time;
            return ($ts > 0) ? $ts : null;
        }

        $ts = strtotime((string)$time);
        return ($ts !== false && $ts > 0) ? $ts : null;
    }
}

if (!function_exists('thai_date')) {
    /**
     * Formats a date into Thai Buddhist Era (พ.ศ.).
     *
     * @param mixed $time Timestamp or date string.
     * @param string $format 'short' (8 ก.ย. 2569), 'full' (8 กันยายน 2569), or 'slash' (08/09/2569)
     * @return string
     */
    function thai_date($time, string $format = 'short'): string
    {
        $ts = parse_thai_time($time);
        if ($ts === null) {
            return '-';
        }

        $thai_months_short = [
            'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
            'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
        ];

        $thai_months_full = [
            'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ];

        $day = date('j', $ts);
        $dayPad = date('d', $ts);
        $monthIndex = (int)date('n', $ts) - 1;
        $monthPad = date('m', $ts);
        $yearBE = (int)date('Y', $ts) + 543;

        if ($format === 'full') {
            $month = $thai_months_full[$monthIndex] ?? '';
            return "$day $month $yearBE";
        }

        if ($format === 'slash') {
            return "$dayPad/$monthPad/$yearBE";
        }

        // Default 'short'
        $month = $thai_months_short[$monthIndex] ?? '';
        return "$day $month $yearBE";
    }
}

if (!function_exists('thai_date_short')) {
    function thai_date_short($time): string
    {
        return thai_date($time, 'short');
    }
}

if (!function_exists('thai_date_full')) {
    function thai_date_full($time): string
    {
        return thai_date($time, 'full');
    }
}

if (!function_exists('thai_date_slash')) {
    function thai_date_slash($time): string
    {
        return thai_date($time, 'slash');
    }
}

if (!function_exists('thai_date_and_time')) {
    /**
     * Formats date and time into Thai Buddhist Era.
     *
     * @param mixed $time Timestamp or date string.
     * @param bool $includeSeconds Whether to include seconds.
     * @param bool $includeThaiWords Whether to prepend 'เวลา' and append 'น.'.
     * @return string
     */
    function thai_date_and_time($time, bool $includeSeconds = false, bool $includeThaiWords = false): string
    {
        $ts = parse_thai_time($time);
        if ($ts === null) {
            return '-';
        }

        $datePart = thai_date($ts, 'short');
        $timePart = $includeSeconds ? date('H:i:s', $ts) : date('H:i', $ts);

        if ($includeThaiWords) {
            return "$datePart เวลา $timePart น.";
        }

        return "$datePart $timePart";
    }
}

if (!function_exists('thai_date_month_year')) {
    /**
     * Formats month and year into Thai Buddhist Era (e.g. กันยายน 2569).
     *
     * @param mixed $time Timestamp or date string.
     * @param bool $full Whether to use full month name.
     * @return string
     */
    function thai_date_month_year($time, bool $full = true): string
    {
        $ts = parse_thai_time($time);
        if ($ts === null) {
            return '-';
        }

        $thai_months_short = [
            'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
            'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
        ];

        $thai_months_full = [
            'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ];

        $monthIndex = (int)date('n', $ts) - 1;
        $yearBE = (int)date('Y', $ts) + 543;
        $month = $full ? ($thai_months_full[$monthIndex] ?? '') : ($thai_months_short[$monthIndex] ?? '');

        return "$month $yearBE";
    }
}
