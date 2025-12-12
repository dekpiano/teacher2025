<?php

if (!function_exists('thai_date_and_time')) {
    function thai_date_and_time($time)
    {
        $thai_months = [
            'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
            'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
        ];

        $date = date('j', $time);
        $month = $thai_months[date('n', $time) - 1];
        $year = date('Y', $time) + 543; // Convert to Buddhist year
        $hour = date('H', $time);
        $minute = date('i', $time);
        $second = date('s', $time);

        return "$date $month $year $hour:$minute:$second";
    }
}
