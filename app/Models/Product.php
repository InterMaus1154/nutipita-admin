<?php

namespace App\Models;

use App\Helpers\ModelResolver;
use App\Models\Scopes\Product\SortByName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

    protected static function booted()
    {
        static::addGlobalScope(new SortByName);
    }

    /*
     * Define relationships
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product', 'product_id', 'order_id')
            ->withPivot('product_qty')
            ->withTimestamps();
    }

    public function customPrices()
    {
        return $this->hasMany(CustomerProductPrice::class, 'product_id', 'product_id');
    }

    public function scopeForCustomer(Builder $query, Customer|int|string $customer)
    {
        $query->afterQuery(function (Collection $products) use ($customer) {
            $products->each(fn(Product $p) => $p->setCurrentCustomer($customer));
        });
    }

    /*
     * Define attributes
     */
    public function getPriceAttribute()
    {
        if (!$this->currentCustomer) {
            abort(418, "Price without customer called on product");
        }
        $price = $this->customPrices
            ->firstWhere('customer_id', $this->currentCustomer->customer_id);
        return $price ? $price->customer_product_price : 0;
    }
}
