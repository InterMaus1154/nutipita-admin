<?php

namespace App\Models;

use App\Helpers\ModelResolver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $primaryKey = 'product_id';
    protected $guarded = [];

    protected ?Customer $currentCustomer = null;

    public function setCurrentCustomer(Customer|int|string $customer)
    {
        $this->currentCustomer = ModelResolver::resolve($customer, Customer::class);
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
        if (!$this->currentCustomer) {
            abort(400, "Price without customer called on product");
        }
        $price = $this->customPrices
            ->firstWhere('customer_id', $this->currentCustomer->customer_id);
        return $price ? $price->customer_product_price : 0;
    }
}
