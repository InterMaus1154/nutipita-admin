<?php
// custom helpers

use App\Helpers\ModelResolver;
use Carbon\Carbon;
use Carbon\WeekDay;
use Illuminate\Database\Eloquent\Model;
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

if(!function_exists('resolveModel')){
    function resolveModel(Model|int|string $id, string $modelClass): Model|null
    {
        return ModelResolver::resolve($id, $modelClass);
    }
}

if(!function_exists('getCurrentWeekNumber')){
    function getCurrentWeekNumber(Carbon|string $date = null): int
    {
        if(is_null($date)){
            $date = now();
        }else{
            $date = dateToCarbon($date, "Week Number input date");
        }

        return Carbon::create($date->year, $date->month, $date->day)
            ->startOfWeek(WeekDay::Sunday)
            ->endOfWeek(WeekDay::Saturday)
            ->week;
    }
}

