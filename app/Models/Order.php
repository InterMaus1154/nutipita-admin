<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $primaryKey = 'order_id';
    protected $guarded = [];

    /*
     * Define relationships
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id')
            ->withPivot('product_qty', 'order_product_unit_price')
            ->withTimestamps();
    }

    /*
     * Define custom attributes
     */
    public function getTotalPitaAttribute()
    {
        return $this->products->sum(function ($product) {
            return $product->pivot->product_qty;
        });
    }

    public function getTotalPriceAttribute()
    {
        return number_format($this->products->sum(function ($product) {
            return $product->pivot->product_qty * $product->pivot->order_product_unit_price;
        }), 2);
    }

    public function getStatusAttribute()
    {
        return ucfirst(OrderStatus::fromName($this->order_status));
    }

    /*
     * Custom methods
     */

    /**
     * Return the sum of provided product for the order
     * @param Product|int $product
     * @return int
     */
    public function getTotalOfProduct(Product|int $product): int
    {

        // if product model is supplied, get id
        $product_id = $product instanceof Product ? $product->product_id : $product;

        if ($this->products->where('product_id', $product_id)) {
            return $this->products->where('product_id', $product_id)->sum(function ($product) {
                return $product->pivot->product_qty;
            });
        }
        return 0;
    }

}
