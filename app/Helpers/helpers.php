<?php
// custom helpers

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

if(!function_exists('dayDate')){
    function dayDate(mixed $date): string
    {
        $parsedDate = Carbon::parse($date);
        return Str::limit($parsedDate->dayName, 3, '').'/'.$parsedDate->format('d/m/Y');
    }
}
