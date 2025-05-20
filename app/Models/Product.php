<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $primaryKey = 'product_id';
    protected $guarded = [];

    protected ?Customer $currentCustomer = null;

    public function setCurrentCustomer(Customer $customer)
    {
        $this->currentCustomer = $customer;
        return $this;
    }


    /*
     * Define relationships
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product')
            ->withPivot('product_qty')
            ->withTimestamps();
    }

    public function customPrices()
    {
        return $this->hasMany(CustomerProductPrice::class, 'product_id', 'product_id');
    }

    /*
     * Define attributes
     */
    public function getPriceAttribute()
    {
        if (!$this->currentCustomer) return $this->attributes['product_unit_price'];

        $price = $this->customPrices()
            ->where('customer_id', $this->currentCustomer->customer_id)
            ->first();
        return $price ? $price->customer_product_price : $this->attributes['product_unit_price'];
    }

}
