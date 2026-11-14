<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

abstract class AbstractFilters
{
    /**
     * @param array<string, mixed> $filters
     * @return AbstractQuery
     */
    public static abstract function from(array $filters): AbstractQuery;
}
