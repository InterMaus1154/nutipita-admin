<?php

namespace App\Infrastructure\Invoice;

use App\Domain\Invoice\Invoice;
use App\Domain\Invoice\InvoiceRepository;
use App\Enums\LegacyInvoiceStatus;
use Illuminate\Database\Eloquent\Builder;

class InvoiceEloquentRepository implements InvoiceRepository
{

    public function allUnpaidSum(): float
    {
        $result = $this->query()
            ->where('invoices.invoice_status', LegacyInvoiceStatus::due->name)
            ->selectRaw('SUM(invoices.invoice_total) as sum')
            ->first();

        return $result->sum ?? 0;
    }

    /**
     * @return Builder<Invoice>
     */
    protected function query(): Builder
    {
        return Invoice::query();
    }
}
