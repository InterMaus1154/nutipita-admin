<?php

namespace App\Enums;

enum OrderStatus: string{
    case PENDING = "pending";
    CASE DELIVERED = "delivered";
    CASE PAID = "paid";
    CASE DELIVERED_AND_PAID = "delivered & paid";
    CASE CANCELLED = "cancelled";
}
