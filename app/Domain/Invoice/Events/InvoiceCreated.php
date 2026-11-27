<?php

namespace App\Domain\Invoice\Events;

use App\Domain\Invoice\Entities\Invoice;
use App\Helpers\Event;

class InvoiceCreated extends Event
{

    public function __construct(public Invoice $invoice)
    {
    }
}
