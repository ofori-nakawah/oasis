<?php


namespace App\Helpers;


class DateFormatter
{
    public static function Parse($date)
    {
        if (strpos($date, '-') !== false) {
            $_date = explode("-",$date);
            $date = $_date[2] ."/".$_date[1]."/".$_date[0];
        }

        return $date;
    }
}
