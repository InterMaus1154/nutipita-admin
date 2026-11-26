<?php

namespace App\Domain\Pdf\Listeners;

use App\Domain\Invoice\Events\InvoiceCreated;
use App\Domain\Pdf\Events\FileFailed;
use App\Domain\Pdf\Events\FileSaved;
use App\Domain\Pdf\Exceptions\CannotGeneratePdfException;
use App\Domain\Pdf\Services\InvoicePdfService;
use App\Helpers\DispatcherInterface;

class InvoiceCreatedGeneratePdf
{
    public function __construct(private InvoicePdfService $pdf, private DispatcherInterface $dispatcher)
    {
    }

    public function handle(InvoiceCreated $event): void
    {
        try {
            $savedPath = $this->pdf->forId($event->invoice->getId())->saveToLocal($event->invoice->getInvoicePath(), disk: 'invoices');
        } catch (\Throwable $e) {
            $failure = $e instanceof CannotGeneratePdfException ? $e : new CannotGeneratePdfException('Pdf generation failed', previous: $e);
            $this->dispatcher->dispatch(new FileFailed($failure));
            return;
        }

        $this->dispatcher->dispatch(new FileSaved($savedPath));
    }
}
