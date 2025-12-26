<?php

namespace MyPlugin\Helpers;

class StringHelper
{
    public static function slugify($text)
    {
        return sanitize_title($text);
    }

    public static function truncate($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }

    public static function formatPrice($amount, $currency = '€')
    {
        return number_format($amount, 2, ',', ' ') . ' ' . $currency;
    }

    public static function generateRandomString($length = 10)
    {
        return wp_generate_password($length, false);
    }

    public static function isEmail($email)
    {
        return is_email($email);
    }

    public static function sanitizeInput($input)
    {
        return sanitize_text_field($input);
    }
}