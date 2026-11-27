<?php

namespace App\Domain\Invoice\Enums;

enum CreateInvoiceMode: string
{
    case AUTO = 'auto';
    case MANUAL = 'manual';
    case SINGLE = 'single';
}
