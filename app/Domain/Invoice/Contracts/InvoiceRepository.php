<?php

namespace App\Domain\Invoice\Contracts;


use App\Domain\Invoice\Data\InvoiceProductData;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Querying\InvoiceQuery;
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

    public function delete(int $id): bool;

    /**
     * @param int $invoiceId
     * @param int[] $orderIds
     * @return bool
     */
    public function linkToOrders(int $invoiceId, array $orderIds): bool;

    /**
     * @param int $invoiceId
     * @param InvoiceProductData[] $invoiceProductData
     * @return bool
     */
    public function linkToProducts(int $invoiceId, array $invoiceProductData): bool;
}
