<?php

namespace App\Enums;

enum OrderStatus: string
{
//    case Y_PENDING = "pending";
    case Y_CONFIRMED = "Confirmed";
//    case G_DELIVERED = "delivered";
    case G_PAID = "Paid";
//    case G_DELIVERED_AND_PAID = "Delivered Paid";
    CASE O_DELIVERED_UNPAID = "Unpaid";
//    case R_DUE_PAYMENT = "Payment Due";
//    case R_CANCELLED = "cancelled";
//    case R_INVALIDATED = "invalidated";
//    case R_UNDEFINED_ORDER_STATUS_FROM_ENUM = "UNDEFINED";

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
        return "";
    }
}
