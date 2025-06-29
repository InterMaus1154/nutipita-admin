<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'order_id', 'order_id');
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
        return $this->products->sum(function ($product) {
            return $product->pivot->product_qty * $product->pivot->order_product_unit_price;
        });
    }

    public function getTotalPriceFormatAttribute(): string
    {
        return "£" . number_format($this->total_price, 2);
    }

    public function getStatusAttribute()
    {
        return ucfirst(OrderStatus::fromName($this->order_status));
    }

    public function getPlacedAtAttribute()
    {
        $date = Carbon::parse($this->order_placed_at);
        return Str::limit($date->dayName, 3, '') . '/' . $date->format('d/m/Y');
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

        // check if order has the product
        if ($this->products->where('product_id', $product_id)) {
            // sum the quantity
            return $this->products->where('product_id', $product_id)->sum(function ($product) {
                return $product->pivot->product_qty;
            });
        }
        return 0;
    }

    /*
     * Other
     */
    public function scopeNonCancelled(Builder $builder)
    {
        return $builder->whereNotIn('order_status', [
            OrderStatus::R_CANCELLED->name,
            OrderStatus::R_INVALIDATED->name
        ]);
    }

}
