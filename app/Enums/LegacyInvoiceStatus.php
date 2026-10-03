<?php

namespace App\Enums;

enum LegacyInvoiceStatus: string
{
    CASE paid = "Paid";
    CASE due = "Unpaid";
    case cancelled = "Cancelled";
}
