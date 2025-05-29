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
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /*
     * Custom methods
     */
    public static function generateInvoiceNumber(): string
    {
        $latestInvoice = Invoice::latest('invoice_id')->first();
        $nextNumber = $latestInvoice ? ((int)substr($latestInvoice->invoice_number, 4)) + 1 : 1;
        return str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
