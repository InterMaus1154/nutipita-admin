<?php

namespace App\Domain\Invoice;

use Illuminate\Support\Str;

enum InvoiceStatus: int
{
    case PAID = 0;
    case UNPAID = 1;
    case CANCELLED = 2;
    case LOSS = 3;

    public function label(): string
    {
        return Str::of($this->name)->lower()->ucfirst()->value();
    }

}
