<?php

namespace App\Domain\Invoice\Strategies;

use App\Domain\Invoice\Data\CreateInvoiceData;
use App\Domain\Invoice\Enums\CreateInvoiceMode;

abstract class AbstractCreateInvoiceStrategy
{
    public abstract function mode(): CreateInvoiceMode;

    public function normalize(): CreateInvoiceData
    {
        return $this->makeCreateInvoiceData();
    }

    public abstract function makeCreateInvoiceData(): CreateInvoiceData;
}
