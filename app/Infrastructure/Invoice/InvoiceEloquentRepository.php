<?php

namespace App\Infrastructure\Invoice;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Data\InvoiceProductData;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Domain\Invoice\Querying\InvoiceQuery;
use App\Helpers\SortOrder;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceEloquentRepository implements InvoiceRepository
{

    public function __construct(private ConnectionInterface $db)
    {
    }

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
        return $this->find(new InvoiceQuery()->id($id))->first();
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

    /**
     * A query shaped for listing: filters plus the select list, eager loads and sorting
     * @param InvoiceQuery|null $query
     * @return Builder<Invoice>
     */
    private function usingQuery(?InvoiceQuery $query = null): Builder
    {
        return $this->shaped($this->filtered($query), $query);
    }

    /**
     * Only the where clauses of the query. No select list, eager loads, joins or ordering,
     * so aggregates and raw selects can be appended freely.
     * @param InvoiceQuery|null $query
     * @return Builder<Invoice>
     */
    private function filtered(?InvoiceQuery $query = null): Builder
    {
        $builder = $this->query();

        if (is_null($query)) {
            return $builder;
        }

        if (isset($query->id)) {
            $builder->where('invoices.invoice_id', $query->id);
        }

        if (isset($query->customerId)) {
            $builder->where('invoices.customer_id', $query->customerId);
        }

        if (isset($query->invoiceDueFrom)) {
            $builder->where('invoices.invoice_due_date', '>=', $query->invoiceDueFrom);
        }

        if (isset($query->invoiceDueTo)) {
            $builder->where('invoices.invoice_due_date', '<=', $query->invoiceDueTo);
        }

        return $builder;
    }

    /**
     * Listing-only parts: eager loads, select list and sorting
     * @param Builder<Invoice> $builder
     * @param InvoiceQuery|null $query
     * @return Builder<Invoice>
     */
    private function shaped(Builder $builder, ?InvoiceQuery $query = null): Builder
    {
        if (is_null($query)) {
            return $builder;
        }

        $relationships = empty($query->relationships) ? ['customer', 'order', 'orders', 'products', 'products.product'] : $query->relationships;

        $builder->with($relationships);

        if (!empty($query->columns)) {
            $builder->select($query->columns === ['*'] ? ['invoices.*'] : $query->columns);
        }

        if (isset($query->sortField)) {
            if (array_key_exists($query->sortField, $this->customSorts())) {
                $builder = $this->customSorts()[$query->sortField]($builder, $query->sortOrder);
            } else {
                $builder->orderBy($query->sortField, $query->sortOrder->value);
            }

            $builder->orderBy('invoices.invoice_id', SortOrder::DESC->value);
        }

        return $builder;
    }

    /**
     * @return array<string, \Closure(Builder, SortOrder): Builder>
     */
    private function customSorts(): array
    {
        return [
            'customer' => function (Builder $builder, SortOrder $order): Builder {
                // without explicit columns the join would return the customer's columns too and overwrite the invoice's
                if (is_null($builder->getQuery()->columns)) {
                    $builder->select('invoices.*');
                }

                return $builder
                    ->join('customers', 'customers.customer_id', '=', 'invoices.customer_id')
                    ->orderBy('customers.customer_name', $order->value);
            },
        ];
    }

    public function paginate(int $perPage = 15, ?InvoiceQuery $query = null): LengthAwarePaginator
    {
        return $this->usingQuery($query)
            ->paginate($perPage);
    }

    public function getInvoiceTotals(?InvoiceQuery $query = null): float
    {
        return $this->filtered($query)
            ->sum('invoices.invoice_total');
    }

    public function getInvoiceCount(?InvoiceQuery $query = null): int
    {
        return $this->filtered($query)
            ->count();
    }

    public function delete(int $id): bool
    {
        return $this->query()
            ->where('invoice_id', $id)
            ->delete();
    }

    /**
     * @param int $invoiceId
     * @param int[] $orderIds
     * @return bool
     */
    public function linkToOrders(int $invoiceId, array $orderIds): bool
    {
        $mapped = collect($orderIds)
            ->map(fn(int $orderId) => ['invoice_id' => $invoiceId, 'order_id' => $orderId])
            ->all();

        return $this->db
            ->table('invoice_orders')
            ->insert($mapped);
    }

    /**
     * @param int $invoiceId
     * @param InvoiceProductData[] $invoiceProductData
     * @return bool
     */
    public function linkToProducts(int $invoiceId, array $invoiceProductData): bool
    {
        $mapped = collect($invoiceProductData)
            ->map(fn(InvoiceProductData $data) => [
                'invoice_id' => $invoiceId,
                'product_id' => $data->productId,
                'product_qty' => $data->quantity,
                'product_unit_price' => $data->unitPrice,
            ])
            ->all();

        return $this->db
            ->table('invoice_products')
            ->insert($mapped);
    }
}
