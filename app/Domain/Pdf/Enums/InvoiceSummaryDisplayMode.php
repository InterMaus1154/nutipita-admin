<?php

namespace App\Domain\Pdf\Enums;

enum InvoiceSummaryDisplayMode: string
{
    case RENDER_PRODUCTS = 'products';
    case RENDER_ORDERS = 'orders';
}
