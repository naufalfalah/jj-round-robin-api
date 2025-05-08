<?php

namespace App\Helpers;

class TextHelper
{
    public static function convertToPlainText($text)
    {
        $text = preg_replace('/\*\*([^*]+)\*\*/', '$1', $text);
        $text = preg_replace('/\* (.*?):/', '$1:', $text);
        $text = preg_replace('/\* (.*)/', '- $1', $text);

        $text = strip_tags($text);

        $text = preg_replace('/\*/', '', $text);

        $text = preg_replace('/\n+/', "\n", $text);

        return $text;
    }

    public static function generateOtp()
    {
        return rand(10000, 99999);
    }
}
