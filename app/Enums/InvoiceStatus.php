<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    CASE paid = "Paid";
    CASE due = "Unpaid";
    case cancelled = "Cancelled";
}
