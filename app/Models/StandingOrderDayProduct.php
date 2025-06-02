<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandingOrderDayProduct extends Model
{
    protected $primaryKey = 'standing_order_day_product_id';
    protected $guarded = [];

    public function day()
    {
        return $this->belongsTo(StandingOrderDay::class, 'standing_order_day_id', 'standing_order_day_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'product_id', 'product_id');
    }
}
