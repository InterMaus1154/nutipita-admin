<?php

namespace App\Domain\Invoice\Entities;

use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\InvoiceProduct;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Invoice extends Model
{
    protected $primaryKey = 'invoice_id';
    protected $guarded = [];

    protected $casts = [
        'invoice_status_new' => InvoiceStatus::class,
        'invoice_from' => 'date',
        'invoice_to' => 'date',
    ];

    /*
     * Define relationships
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function products()
    {
        return $this->hasMany(InvoiceProduct::class, 'invoice_id', 'invoice_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'invoice_orders', 'invoice_id', 'order_id', 'invoice_id', 'order_id');
    }

    public function creditNote()
    {
        return $this->hasOne(CreditNote::class, 'invoice_id', 'invoice_id');
    }

    public function getInvoiceStatus(): InvoiceStatus
    {
        return $this->getAttribute('invoice_status_new');
    }

    public function setInvoiceStatus(InvoiceStatus $status): void
    {
        $this->setAttribute('invoice_status_new', $status);
    }

    public function getInvoiceNumber(): string
    {
        return $this->getAttribute('invoice_number');
    }

    public function getId(): int
    {
        return $this->getAttribute('invoice_id');
    }

    public function getInvoiceFrom(): ?Carbon
    {
        return $this->getAttribute('invoice_from');
    }

    public function getInvoiceTo(): ?Carbon
    {
        return $this->getAttribute('invoice_to');
    }

    public function getRawDeliveryCharge(): ?string
    {
        return $this->getAttribute('invoice_delivery_charge');
    }

    public function getDeliveryCharge(): DeliveryCharge
    {
        return DeliveryCharge::fromString($this->getRawDeliveryCharge());
    }

    public function getInvoiceTotal(): float
    {
        return $this->getAttribute('invoice_total');
    }

    /*
     * Custom methods
     */
    public static function getNextInvoiceNumber(string $latestBy = "invoice_number"): string
    {
        $latestInvoice = Invoice::latest($latestBy)->first();
        if ($latestInvoice) {
            $oldNumber = (int)$latestInvoice->invoice_number;
            $nextNumber = $oldNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getInvoicePath(): string
    {
        return $this->getAttribute('invoice_path');
    }

    /**
     * @return array
     */
    public function getOrderIds(): array
    {
        return $this->orders->pluck('order_id')->all();
    }

}
