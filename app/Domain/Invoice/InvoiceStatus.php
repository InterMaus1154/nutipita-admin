<?php

namespace App\Domain\Invoice;

use Illuminate\Support\Str;

enum InvoiceStatus: int
{
    case PAID = 0;
    /** Sometimes referred to as due */
    case UNPAID = 1;
    case CANCELLED = 2;
    case LOSS = 3;

    public function label(): string
    {
        return Str::of($this->name)->lower()->ucfirst()->value();
    }

    public function backgroundColor(): string
    {
        return match($this){
            self::PAID => 'bg-green-500!',
            self::UNPAID => 'bg-red-500!',
            self::CANCELLED => 'bg-orange-400!',
            self::LOSS => 'bg-yellow-400!',
        };
    }

    public function shadowColor(): string
    {
        return match($this){
            self::PAID => "oklch(72.3% 0.219 149.579)",
            self::UNPAID => "oklch(63.7% 0.237 25.331)",
            self::CANCELLED => "oklch(75% 0.183 55.934)",
            self::LOSS => "oklch(85.2% 0.199 91.936)",
        };
    }

}
