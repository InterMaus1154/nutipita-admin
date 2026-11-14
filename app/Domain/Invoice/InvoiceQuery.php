<?php

namespace App\Domain\Invoice;

use App\Helpers\AbstractQuery;
use App\Helpers\QueryCustomerId;
use App\Helpers\QueryCustomerIdTrait;
use App\Helpers\SortableQuery;
use App\Helpers\SortableQueryTrait;
use App\Helpers\SortOrder;

class InvoiceQuery extends AbstractQuery implements QueryCustomerId, SortableQuery
{
    use QueryCustomerIdTrait, SortableQueryTrait;

    public string $invoiceDueFrom;
    public string $invoiceDueTo;

    public bool $hasCustomerSort = false;

    public function invoiceDueFrom(string $value): self
    {
        $this->invoiceDueFrom = $value;
        return $this;
    }

    public function invoiceDueTo(string $value): self
    {
        $this->invoiceDueTo = $value;
        return $this;
    }

    public function hasCustomerSort(SortOrder $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        $this->hasCustomerSort = true;
        return $this;
    }
}
