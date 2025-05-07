<?php

if (!function_exists('removePrefix')) {
    function removePrefix($string)
    {
        return str_replace('customers/', '', $string);
    }
}
