<?php

namespace App\Helpers;

interface SortableQuery
{
    public function sortBy(string $field, SortOrder $order): static;
}
