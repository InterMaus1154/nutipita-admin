<?php

namespace App\Helpers;

trait SortableQueryTrait
{
    public string $sortField;
    public SortOrder $sortOrder = SortOrder::DESC;


    public function sortBy(string $field, SortOrder $order = SortOrder::DESC): static
    {
        $this->sortField = $field;
        $this->sortOrder = $order;
        return $this;
    }
}
