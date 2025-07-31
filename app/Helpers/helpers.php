<?php
// custom helpers

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Helpers\Format;

if (!function_exists('dayDate')) {
    /**
     * Returns date in dayName/day/month/year format
     * @param Carbon|string $dateInput
     * @return string
     */
    function dayDate(Carbon|string $dateInput): string
    {
        return Format::dayDate($dateInput);
    }
}

if (!function_exists('dateToCarbon')) {
    /**
     * Takes an input, and tries to return as a Carbon date instance
     * @param Carbon|string|null $inputDate
     * @param string $fieldName
     * @param bool $nullable
     * @return Carbon|null
     */
    function dateToCarbon(Carbon|string|null $inputDate, string $fieldName, bool $nullable = false): Carbon|null
    {
        return Format::dateToCarbon($inputDate, $fieldName, $nullable);
    }
}

if (!function_exists('roundUp')) {
    /**
     * Always round up to 2 decimal places
     * @param $amount
     * @param int $precision
     * @return float
     */
    function roundUp($amount, int $precision = 2): float
    {
        return Format::roundUp($amount, $precision);
    }
}

//if (!function_exists('formatMoney')) {
//    /**
//     * Format money for 2 or 3 decimal places
//     * If 3 by default, leave it as it is
//     * Never shows more than 2 significant decimal places
//     * For example
//     * 0.2550 -> 0.255
//     * 0.2500 -> 0.25
//     * 0.2 -> 0.20
//     * 500 -> 500.00
//     * @param mixed $amount
//     * @return string
//     */
//    function formatMoney(mixed $amount): string
//    {
//        // convert to float
//        try {
//            $amount = (float)$amount;
//        } catch (Exception $e) {
//            return "ERROR IN MONEY FORMATTING, CANNOT CONVERT TO FLOAT!";
//        }
//
//        // enforce 3 decimal digits, and proper number format
//        $formatted = number_format($amount, 3, '.', ',');
//
//        // remove trailing zeros (from the right), then remove the decimal separator
//        $formatted = rtrim(rtrim($formatted, '0'), '.');
//
//        // if decimal separator is found (eg 0.255)
//        if (str_contains($formatted, '.')) {
//            // find decimal separator
//            $decimalSeparatorPos = strpos($formatted, '.');
//
//            // get the decimal part
//            $decimals = substr($formatted, $decimalSeparatorPos + 1);
//            // if the num of decimals are less than 2, add a trailing zero to make it 2 decimals, eg 0.2 -> 0.20
//            if (strlen($decimals) < 2) {
//                $formatted = $formatted . '0';
//            }
//        } else {
//            // if no decimal separator, add trailing zero digits for proper money format
//            $formatted = $formatted . '.00';
//        }
//        return $formatted;
//    }


if (!function_exists('numberFormat')) {
    /**
     * Return a formatted string to proper number, with 2 decimals max (default)
     * @param string|int|float $amount
     * @param int $decimals
     * @return string
     */
    function numberFormat(string|int|float $amount, int $decimals = 2): string
    {
        return Format::numberFormat($amount, $decimals);
    }
}


if (!function_exists('amountFormat')) {
    /**
     * Format number properly, without any decimals
     * @param string|int|float $amount
     * @return string
     */
    function amountFormat(string|int|float $amount): string
    {
        return Format::numberFormat($amount, 0);
    }
}

if (!function_exists('moneyFormat')) {
    function moneyFormat($amount): string
    {
        return '£'.Format::numberFormat(roundUp($amount, 2), 2);
    }
}

if (!function_exists('unitPriceFormat')) {
    /**
     * Convert to unit price format.
     * Eg: 0.2500 -> 0.25, 0.2550 -> 0.255, 0.2 -> 0.20
     * @param string|int|float $amount
     * @return string
     */
    function unitPriceFormat(string|int|float $amount): string
    {
        return '£'.Format::unitPriceFormat($amount);
    }
}
