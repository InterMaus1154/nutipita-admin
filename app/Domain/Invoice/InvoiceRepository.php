<?php

namespace App\Domain\Invoice;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface InvoiceRepository
{
    /**
     * Total sum of the unpaid invoices
     * @return float
     */
    public function allUnpaidSum(): float;

    public function findById(int $id): ?Invoice;

    /**
     * @param InvoiceQuery $query
     * @return Collection<int, Invoice>
     */
    public function find(InvoiceQuery $query): Collection;

    public function commit(Invoice $invoice): Invoice;

    /**
     * @param InvoiceQuery|null $query
     * @return Builder<Invoice>
     */
    public function usingQuery(?InvoiceQuery $query = null): Builder;

    /**
     * @param int $perPage
     * @param InvoiceQuery|null $query
     * @return LengthAwarePaginator<int, Invoice>
     */
    public function paginate(int $perPage = 15, ?InvoiceQuery $query = null): LengthAwarePaginator;

    /**
     * Sum of invoice totals matching the query
     * @param InvoiceQuery|null $query
     * @return float
     */
    public function getInvoiceTotals(?InvoiceQuery $query = null): float;

    /**
     * Number of invoices matching the query
     * @param InvoiceQuery|null $query
     * @return int
     */
    public function getInvoiceCount(?InvoiceQuery $query = null): int;
}
