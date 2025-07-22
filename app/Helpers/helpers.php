<?php
// custom helpers

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

if (!function_exists('dayDate')) {
    /**
     * Returns date in dayName/day/month/year format
     * @param mixed $date
     * @return string
     */
    function dayDate(mixed $date): string
    {
        $parsedDate = Carbon::parse($date);
        return Str::limit($parsedDate->dayName, 3, '') . '/' . $parsedDate->format('d/m/Y');
    }
}

if (!function_exists('formatMoney')) {
    /**
     * Format money for 2 or 3 decimal places
     * If 3 by default, leave it as it is
     * Never shows more than 2 significant decimal places
     * For example
     * 0.2550 -> 0.255
     * 0.2500 -> 0.25
     * 0.2 -> 0.20
     * 500 -> 500.00
     * @param mixed $amount
     * @return string
     */
    function formatMoney(mixed $amount): string
    {
        // convert to float
        try {
            $amount = (float)$amount;
        } catch (Exception $e) {
            return "ERROR IN MONEY FORMATTING, CANNOT CONVERT TO FLOAT!";
        }

        // enforce 3 decimal digits, and proper number format
        $formatted = number_format($amount, 3, '.', ',');

        // remove trailing zeros (from the right), then remove the decimal separator
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        // if decimal separator is found (eg 0.255)
        if (str_contains($formatted, '.')) {
            // find decimal separator
            $decimalSeparatorPos = strpos($formatted, '.');

            // get the decimal part
            $decimals = substr($formatted, $decimalSeparatorPos + 1);
            // if the num of decimals are less than 2, add a trailing zero to make it 2 decimals, eg 0.2 -> 0.20
            if (strlen($decimals) < 2) {
                $formatted = $formatted . '0';
            }
        } else {
            // if no decimal separator, add trailing zero digits for proper money format
            $formatted = $formatted . '.00';
        }
        return $formatted;
    }
}

if (!function_exists('formatMoneyCurrency')) {

    /**
     * Same as formatMoney, but with a prefix of chosen currency
     * default is £ (pound)
     * @param mixed $amount
     * @param string $currencySign
     * @return string
     */
    function formatMoneyCurrency(mixed $amount, string $currencySign = '£'): string
    {
        return '£' . formatMoney($amount);
    }
}
