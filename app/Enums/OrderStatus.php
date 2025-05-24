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
    case UNDEFINED_ORDER_STATUS_FROM_ENUM = "UNDEFINED_ORDER_STATUS_FROM_ENUM";

    public static function fromName(string $name): string
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case->value;
            }
        }
        return self::UNDEFINED_ORDER_STATUS_FROM_ENUM->value;
    }
}
