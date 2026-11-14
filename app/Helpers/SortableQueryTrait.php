<?php

namespace App\Helpers;

trait SortableQueryTrait
{
    public string $field;
    public SortOrder $sortOrder;


    public function sortBy(string $field, SortOrder $order): self
    {
        $this->field = $field;
        $this->sortOrder = $order;
    }
}
