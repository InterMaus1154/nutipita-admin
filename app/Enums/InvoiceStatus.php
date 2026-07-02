<?php

namespace NutiPita\Enums;

enum InvoiceStatus: string
{
    CASE paid = "Paid";
    CASE due = "Unpaid";
    case cancelled = "Cancelled";
}
