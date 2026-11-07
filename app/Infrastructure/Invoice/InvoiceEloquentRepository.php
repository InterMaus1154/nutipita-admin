<?php

namespace App\Infrastructure\Invoice;

use App\Domain\Invoice\Invoice;
use App\Domain\Invoice\InvoiceRepository;
use App\Domain\Invoice\InvoiceStatus;
use Illuminate\Database\Eloquent\Builder;

class InvoiceEloquentRepository implements InvoiceRepository
{

    public function allUnpaidSum(): float
    {
        $result = $this->query()
            ->where('invoices.invoice_status_new', InvoiceStatus::UNPAID->value)
            ->selectRaw('SUM(invoices.invoice_total) as sum')
            ->first();

        return $result->sum ?? 0;
    }

    public function findById(int $id): ?Invoice
    {
        return $this->query()
            ->where('invoice_id', $id)
            ->with(['products', 'customer', 'products.product'])
            ->first();
    }

    /**
     * @return Builder<Invoice>
     */
    protected function query(): Builder
    {
        return Invoice::query();
    }
}
