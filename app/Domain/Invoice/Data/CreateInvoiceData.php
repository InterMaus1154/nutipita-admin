<?php

namespace App\Domain\Invoice\Data;

use App\Domain\Invoice\Entities\DeliveryCharge;
use Carbon\Carbon;

final readonly class CreateInvoiceData
{
    /**
     * @param int $customerId
     * @param Carbon $issueDate
     * @param Carbon $dueDate
     * @param string $number
     * @param ?DeliveryCharge $deliveryCharge
     * @param array<int, InvoiceProductData> $products
     * @param array $orderIds
     * @param Carbon|null $from
     * @param Carbon|null $to
     */
    public function __construct(public int $customerId,
                                public Carbon $issueDate,
                                public Carbon $dueDate,
                                public string $number,
                                public ?DeliveryCharge $deliveryCharge,
                                public array $products,
                                public array $orderIds = [],
                                public ?Carbon $from = null,
                                public ?Carbon $to = null,
    )
    {
    }
}
