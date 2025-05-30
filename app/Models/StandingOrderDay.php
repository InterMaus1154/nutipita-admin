<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandingOrderDay extends Model
{
    protected $primaryKey = 'standing_order_day_id';
    protected $guarded = [];

    public function standingOrder()
    {
        return $this->belongsTo(StandingOrder::class, 'standing_order_id', 'standing_order_id');
    }
}
