<?php

namespace App\Helpers;

class TextMasker
{
    /**
     * Mask phone numbers and email addresses inside an HTML string.
     * Hides direct-contact details so guests must sign in to retrieve them.
     */
    public static function maskContactInfo(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $masked = preg_replace_callback(
            '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i',
            function ($match) {
                $email = $match[0];
                [$local, $domain] = explode('@', $email, 2);
                $visible = mb_substr($local, 0, 2);
                return $visible . str_repeat('•', max(1, mb_strlen($local) - 2)) . '@' . $domain;
            },
            $html
        );

        $masked = preg_replace_callback(
            '/(?:(?:\+?\d[\s\-().]?){7,}\d)/',
            function ($match) {
                $digits = preg_replace('/\D/', '', $match[0]);
                if (strlen($digits) < 7) {
                    return $match[0];
                }
                $last = substr($digits, -2);
                return str_repeat('•', strlen($digits) - 2) . $last;
            },
            $masked
        );

        return $masked;
    }
}
