<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialRecord extends Model
{
    protected $table = 'financial_records';
    protected $primaryKey = 'fin_record_id';
    protected $guarded = [];

}
