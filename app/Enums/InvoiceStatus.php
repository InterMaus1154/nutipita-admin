<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    CASE paid = "Paid";
    CASE due = "Due";
    case cancelled = "Cancelled";
}
