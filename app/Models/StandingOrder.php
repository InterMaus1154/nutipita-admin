<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandingOrder extends Model
{
    protected $primaryKey = 'standing_order_id';
    protected $guarded = [];

    public function standingOrderDays()
    {
        return $this->hasMany(StandingOrderDay::class, 'standing_order_id', 'standing_order_id');
    }
}
