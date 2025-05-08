<?php

if (!function_exists('generateRandomNumberString')) {
    function generateRandomNumberString($length = 12)
    {
        $numbers = '0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $numbers[rand(0, strlen($numbers) - 1)];
        }

        return $randomString;
    }
}

if (!function_exists('convertNumberFormat')) {
    function convertNumberFormat($number)
    {
        $number = preg_replace('/\D/', '', $number);

        $length = strlen($number);
        if ($length == 10) {
            return preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $number);
        } elseif ($length == 11) {
            return preg_replace('/(\d{3})(\d{4})(\d{4})/', '$1-$2-$3', $number);
        } elseif ($length == 12) {
            return preg_replace('/(\d{4})(\d{4})(\d{4})/', '$1-$2-$3', $number);
        } else {
            return $number;
        }
    }
}

if (!function_exists('convertPhoneNumber')) {
    function convertPhoneNumber($phoneNumber, $countryCode = '+65')
    {
        if (substr($phoneNumber, 0, 1) === '0') {
            return $countryCode.substr($phoneNumber, 1);
        }

        if (substr($phoneNumber, 0, 1) === '+') {
            return $phoneNumber;
        }

        return $countryCode.$phoneNumber;
    }
}

if (!function_exists('arrayToObject')) {
    function arrayToObject($array)
    {
        if (is_array($array)) {
            return (object) array_map('arrayToObject', $array);
        }

        return $array;
    }
}
