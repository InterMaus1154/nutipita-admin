<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialRecord extends Model
{
    protected $table = 'financial_records';
    protected $primaryKey = 'fin_record_id';
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(FinancialCategory::class, 'fin_cat_id', 'fin_cat_id');
    }

}
