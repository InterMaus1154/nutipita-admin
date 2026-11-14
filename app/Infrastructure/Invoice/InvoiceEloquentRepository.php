<?php

namespace App\Infrastructure\Invoice;

use App\Domain\Invoice\Invoice;
use App\Domain\Invoice\InvoiceQuery;
use App\Domain\Invoice\InvoiceRepository;
use App\Domain\Invoice\InvoiceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function commit(Invoice $invoice): Invoice
    {
        return tap($invoice)->save();
    }

    /**
     * @param InvoiceQuery $query
     * @return Collection<int, Invoice>
     */
    public function find(InvoiceQuery $query): Collection
    {
        return $this->usingQuery($query)->get();
    }

    /**
     * @return Builder<Invoice>
     */
    protected function query(): Builder
    {
        return Invoice::query();
    }

    public function usingQuery(?InvoiceQuery $query = null): Builder
    {
        $builder = $this->query();

        if (is_null($query)) {
            return $builder;
        }

        $builder->with($query->relationships);
        $builder->select($query->columns === ['*'] ? ['invoices.*'] : $query->columns);

        if (isset($query->customerId)) {
            $builder->where('invoices.customer_id', $query->customerId);
        }

        if (isset($query->invoiceDueFrom)) {
            $builder->where('invoices.invoice_due_date', '>=', $query->invoiceDueFrom);
        }

        if (isset($query->invoiceDueTo)) {
            $builder->where('invoices.invoice_due_date', '<=', $query->invoiceDueTo);
        }

        if ($query->hasCustomerSort) {
            $builder->join('customers', 'customers.customer_id', '=', 'invoices.customer_id')
                ->orderBy('customers.customer_name', $query->sortOrder->value);
        }

        return $builder;
    }

    public function paginate(int $perPage = 15, ?InvoiceQuery $query = null): LengthAwarePaginator
    {
        return $this->usingQuery($query)
            ->paginate($perPage);
    }

    public function getInvoiceTotals(?InvoiceQuery $query = null): float
    {
        return $this->usingQuery($query)
            ->sum('invoices.invoice_total');
    }

    public function getInvoiceCount(?InvoiceQuery $query = null): int
    {
        return $this->usingQuery($query)
            ->count();
    }
}
