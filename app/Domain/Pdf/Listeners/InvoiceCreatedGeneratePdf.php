<?php

namespace App\Domain\Pdf\Listeners;

use App\Domain\Invoice\Events\InvoiceCreated;
use App\Domain\Pdf\Services\InvoicePdfService;

class InvoiceCreatedGeneratePdf
{
    public function __construct(private InvoicePdfService $pdf)
    {
    }

    public function handle(InvoiceCreated $event): void
    {
        $this->pdf->forId($event->invoice->getId())->saveToLocal($event->invoice->getInvoicePath());
    }
}
