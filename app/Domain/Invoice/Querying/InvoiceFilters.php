<?php

namespace App\Domain\Invoice\Querying;

use App\Helpers\AbstractFilters;

class InvoiceFilters extends AbstractFilters
{

    /**
     * @param array<string, mixed> $filters
     * @return InvoiceQuery
     */
    public static function from(array $filters): InvoiceQuery
    {
        $query = new InvoiceQuery();

        if (!empty($filters['customer_id'])) {
            $query->customerId($filters['customer_id']);
        }

        if (!empty($filters['invoice_from'])) {
            $query->invoiceDueFrom($filters['invoice_from']);
        }

        if (!empty($filters['invoice_to'])) {
            $query->invoiceDueTo($filters['invoice_to']);
        }

        $query->relationships(['customer', 'orders', 'order', 'products', 'products.product']);

        return $query;
    }
}
