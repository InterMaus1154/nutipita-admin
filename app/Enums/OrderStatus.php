<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = "pending";
    case CONFIRMED = "confirmed";
    case DELIVERED = "delivered";
    case PAID = "paid";
    case DELIVERED_AND_PAID = "delivered & paid";
    case DUE_PAYMENT = "due_payment";
    case CANCELLED = "cancelled";
    case INVALIDATED = "invalidated";
}
