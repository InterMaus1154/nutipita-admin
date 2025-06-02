<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandingOrderDay extends Model
{
    protected $primaryKey = 'standing_order_day_id';
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(StandingOrder::class, 'standing_order_id', 'standing_order_id');
    }

    public function products()
    {
        return $this->hasMany(StandingOrderDayProduct::class, 'standing_order_day_id', 'standing_order_day_id');
    }
}
