<?php

namespace App\Domain\Invoice\Querying;

use App\Helpers\AbstractQuery;
use App\Helpers\QueryCustomerId;
use App\Helpers\QueryCustomerIdTrait;
use App\Helpers\SortableQuery;
use App\Helpers\SortableQueryTrait;

final class InvoiceQuery extends AbstractQuery implements QueryCustomerId, SortableQuery
{
    use QueryCustomerIdTrait, SortableQueryTrait;

    public string $invoiceDueFrom;
    public string $invoiceDueTo;

    public string $invoiceNumber;

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

    public function invoiceNumber(string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }
}
