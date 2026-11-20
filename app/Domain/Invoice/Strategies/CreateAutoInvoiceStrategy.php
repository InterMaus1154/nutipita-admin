<?php

namespace App\Domain\Invoice\Strategies;

use App\Domain\Invoice\Enums\CreateInvoiceMode;
use App\Domain\Invoice\Strategies\AbstractCreateInvoiceStrategy;

class CreateAutoInvoiceStrategy extends AbstractCreateInvoiceStrategy
{

    public function mode(): CreateInvoiceMode
    {
        return CreateInvoiceMode::AUTO;
    }
}
