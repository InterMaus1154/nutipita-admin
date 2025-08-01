<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $primaryKey = 'invoice_id';
    protected $guarded = [];

    /*
     * Define relationships
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function products()
    {
        return $this->hasMany(InvoiceProduct::class, 'invoice_id', 'invoice_id');
    }

    /*
     * Custom methods
     */
    public static function generateInvoiceNumber(): string
    {
        $latestInvoice = Invoice::latest('invoice_id')->first();
        if ($latestInvoice) {
            $oldNumber = (int)$latestInvoice->invoice_number;
            $nextNumber = $oldNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
