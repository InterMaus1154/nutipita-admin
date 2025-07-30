<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    protected $table = 'invoice_products';
    public $timestamps = false;
    protected $primaryKey = false;
    protected $guarded = [];
}
