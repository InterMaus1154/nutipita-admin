<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandingOrder extends Model
{
    protected $primaryKey = 'standing_order_id';
    protected $guarded = [];

    public function days()
    {
        return $this->hasMany(StandingOrderDay::class, 'standing_order_id', 'standing_order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
