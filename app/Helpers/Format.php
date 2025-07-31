<?php

namespace App\Helpers;

use Carbon\Carbon;

final class Format
{

    public static function dayDate($input)
    {

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
        if($inputDate instanceof Carbon){
            return $inputDate;
        }

        // if the value provided is null, AND null value is allowed, return null
        if(is_null($inputDate) && $nullable){
            return null;
        }

        //finally, try to parse the date to a Carbon instance
        try{
            return Carbon::parse($inputDate);
        }catch(\Exception $e){
            // if invalid format is provided, that cannot be parsed
            throw new \InvalidArgumentException("Invalid date format for {$fieldName}", 422);
        }
    }

}
