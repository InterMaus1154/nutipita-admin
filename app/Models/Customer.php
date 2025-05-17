<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    protected $primaryKey = 'customer_id';
    protected $guarded = [];

    /*
     * Define relationships
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'customer_id');
    }

    public function customPrices()
    {
        return $this->hasMany(CustomerProductPrice::class, 'customer_id', 'customer_id');
    }

}
