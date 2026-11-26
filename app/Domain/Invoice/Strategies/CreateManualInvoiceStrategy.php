<?php

namespace App\Domain\Invoice\Strategies;

use App\Domain\Invoice\Data\CreateInvoiceData;
use App\Domain\Invoice\Enums\CreateInvoiceMode;
use App\Domain\Invoice\Strategies\AbstractCreateInvoiceStrategy;

class CreateManualInvoiceStrategy extends AbstractCreateInvoiceStrategy
{

    public function mode(): CreateInvoiceMode
    {
        return CreateInvoiceMode::MANUAL;
    }

    public function makeCreateInvoiceData(): CreateInvoiceData
    {
        // TODO: Implement makeCreateInvoiceData() method.
    }
}
