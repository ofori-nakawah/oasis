<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Log;

class DateFormatter
{
    public static function Parse($date)
    {
        if (strpos($date, '/') !== false) {
            $_date = explode("/", $date);
            if ($_date[1] > 12) {
                if (strlen($_date[0]) > 2) {
                    $date = $_date[0] ."-".$_date[2]."-".$_date[1];
                } else {
                    $date = $_date[2] ."-".$_date[0]."-".$_date[1];
                }
            } else {
                if (strlen($_date[0]) > 2) {
                    $date = $_date[0] ."-".$_date[1]."-".$_date[2];
                } else {
                    $date = $_date[2] ."-".$_date[1]."-".$_date[0];
                }
            }
        }

        return $date;
    }
}
