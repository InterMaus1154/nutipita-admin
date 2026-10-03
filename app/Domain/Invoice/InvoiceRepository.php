<?php

namespace App\Domain\Invoice;


interface InvoiceRepository
{
    /**
     * Total sum of the unpaid invoices
     * @return float
     */
    public function allUnpaidSum(): float;
}
