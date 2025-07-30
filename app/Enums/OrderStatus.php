<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Y_PENDING = "pending";
    case Y_CONFIRMED = "confirmed";
    case G_DELIVERED = "delivered";
    case G_PAID = "paid";
    case G_DELIVERED_AND_PAID = "delivered & paid";
    case R_DUE_PAYMENT = "payment due";
    case R_CANCELLED = "cancelled";
    case R_INVALIDATED = "invalidated";
    case R_UNDEFINED_ORDER_STATUS_FROM_ENUM = "UNDEFINED_ORDER_STATUS_FROM_ENUM";

    /**
     * Returns an enum value if there is a matching case
     * @param string $name
     * @return string
     */
    public static function fromName(string $name): string
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case->value;
            }
        }
        return self::R_UNDEFINED_ORDER_STATUS_FROM_ENUM->value;
    }
}
