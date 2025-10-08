<?php


namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DateFormatter
{
    public static function Parse($date)
    {
        if (strpos($date, '/') !== false) {
            $_date = explode("/", $date);
            if ($_date[1] > 12) {
                if (strlen($_date[0]) > 2) {
                    $date = $_date[0] . "-" . $_date[2] . "-" . $_date[1];
                } else {
                    $date = $_date[2] . "-" . $_date[0] . "-" . $_date[1];
                }
            } else {
                if (strlen($_date[0]) > 2) {
                    $date = $_date[0] . "-" . $_date[1] . "-" . $_date[2];
                } else {
                    $date = $_date[2] . "-" . $_date[1] . "-" . $_date[0];
                }
            }
        }

        return $date;
    }

    public static function ParseFlexibleDate(string $inputDate)
    {
        $inputDate = trim($inputDate);

        // Normalize separators
        $normalizedDate = str_replace('-', '/', $inputDate);

        // Define possible date formats
        $formats = [
            'd/m/Y',           // e.g., 25/01/2025
            'd/m/Y H:i',       // e.g., 25/01/2025 14:30
            'D M d Y',         // e.g., Sat Jan 25 2025
            'D M d Y H:i:s',   // e.g., Sat Jan 25 2025 14:30:00
            'Y-m-d',           // e.g., 2025-01-25
            'Y-m-d H:i:s',     // e.g., 2025-01-25 14:30:00
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $normalizedDate);
                if ($date !== false) {
                    return $date;
                }
            } catch (\Exception $e) {
                // Continue to next format
            }
        }

        // Attempt to parse using Carbon's parse method as a fallback
        try {
            return Carbon::parse($inputDate);
        } catch (\Exception $e) {
            // Parsing failed
            return null;
        }
    }
}
