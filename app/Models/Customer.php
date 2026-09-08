<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    protected $primaryKey = 'customer_id';
    protected $guarded = [];


    /*
     * Define relationships
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'customer_id');
    }

    public function customPrices()
    {
        return $this->hasMany(CustomerProductPrice::class, 'customer_id', 'customer_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id', 'customer_id');
    }

    /*
     * Define attributes
     */

    public function getShortAddressAttribute()
    {
        return $this->customer_address_1 . ", " . $this->customer_city . ", " . $this->customer_postcode;
    }

    public function markHidden(): void
    {
        $this->setAttribute('is_hidden', true);
    }

    public function markVisible(): void
    {
        $this->setAttribute('is_hidden', false);
    }

    public function getIsHidden(): bool
    {
        if(is_null($this->getAttribute('is_hidden'))){
            return false;
        }
        return $this->getAttribute('is_hidden');
    }

    public function toggleHidden(): void
    {
        if($this->getIsHidden()){
            $this->markVisible();
        }else{
            $this->markHidden();
        }
    }

    public function getId(): int
    {
        return $this->getAttribute('customer_id');
    }

}
