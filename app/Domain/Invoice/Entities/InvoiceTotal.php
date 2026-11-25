<?php

namespace App\Domain\Invoice\Entities;

class InvoiceTotal
{
    public function __construct(private Invoice $invoice)
    {
    }

    public function hasCredit(): bool
    {
        return $this->invoice->hasCredit();
    }

    public function credit(): ?float
    {
        return $this->invoice->getCredit();
    }

    public function subtotal(): float
    {
        return $this->invoice->getInvoiceTotal();
    }

    public function total(): float
    {
        if(!$this->hasCredit()) {
            return $this->subtotal();
        }

        return $this->subtotal() - $this->credit();
    }
}
