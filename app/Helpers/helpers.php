<?php

if (!function_exists('testHelper')) {
    function testHelper()
    {
        return "Helper fonctionne !";
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($value, $decimals = 0, $decimalSeparator = ',', $thousandsSeparator = ' ')
    {
        $value = floatval($value ?? 0);
        return number_format($value, $decimals, $decimalSeparator, $thousandsSeparator);
    }
}