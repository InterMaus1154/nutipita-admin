<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Str;

final class Format
{
    /**
     * Returns date in dayName/day/month/year format
     * @param Carbon|string $dateInput
     * @return string
     */
    public static function dayDate(Carbon|string $dateInput): string
    {
        $date = self::dateToCarbon($dateInput, "Day Date", false);
        return Str::limit($date->dayName, 3, '') . '/' . $date->format('d/m/Y');
    }

    /**
     * Takes an input, and tries to return as a Carbon date instance
     * @param Carbon|string|null $inputDate
     * @param string $fieldName
     * @param bool $nullable
     * @return Carbon|null
     */
    public static function dateToCarbon(Carbon|string|null $inputDate, string $fieldName, bool $nullable = false): Carbon|null
    {
        // if the input is an instance of Carbon by default, simply return it
        if ($inputDate instanceof Carbon) {
            return $inputDate;
        }

        // if the value provided is null, AND null value is allowed, return null
        if (is_null($inputDate) && $nullable) {
            return null;
        }

        //finally, try to parse the date to a Carbon instance
        try {
            return Carbon::parse($inputDate);
        } catch (\Exception $e) {
            // if invalid format is provided, that cannot be parsed
            throw new \InvalidArgumentException("Invalid date format for {$fieldName}", 422);
        }
    }

    /**
     * Convert to unit price format.
     * Eg: 0.2500 -> 0.25, 0.2550 -> 0.255, 0.2 -> 0.20
     * @param string|int|float $amount
     * @return string
     */
    public static function unitPriceFormat(string|int|float $amount): string
    {
        // convert to float
        try {
            $amount = (float)$amount;
        } catch (\Exception $e) {
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

    /**
     * Return a formatted string to proper number, with 2 decimals max (default)
     * @param string|int|float $amount
     * @param int $decimals
     * @return string
     */
    public static function numberFormat(string|int|float $amount, int $decimals = 2): string
    {
        return number_format($amount, $decimals);
    }

    /**
     * Always round up to 2 decimal places
     * @param $amount
     * @param int $precision
     * @return float
     */
    public static function roundUp($amount, int $precision = 2): float
    {
        $factor = pow(10, $precision);
        return ceil($amount * $factor) / $factor;
    }

    /**
     * Returns year from a date as int
     * @param string|Carbon $date
     * @return int
     */
    public static function getYearFromDate(string|Carbon $date): int
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->year;
    }


}
