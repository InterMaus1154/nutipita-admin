<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialCategory extends Model
{
    protected $primaryKey = 'fin_cat_id';
    public $timestamps = false;
    protected $table = 'financial_categories';


}
