<?php


namespace App\Helpers;


class DateFormatter
{
    public static function Parse($date)
    {
        if (strpos($date, '-') !== false) {
            $date = str_replace("-", "/", strrev($date));
        }

        $array = explode('/', $date);
        $tmp = $array[0];
        $array[0] = $array[1];
        $array[1] = $tmp;
        unset($tmp);
        return implode('/', $array);
    }
}
