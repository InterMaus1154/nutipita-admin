<?php

namespace App\Enums;

/**
 * @deprecated use app/Domain/Invoice/InvoiceStatus.php
 */
enum LegacyInvoiceStatus: string
{
    CASE paid = "Paid";
    CASE due = "Unpaid";
    case cancelled = "Cancelled";
}
