<?php

namespace App\DataTransferObjects;

use App\Helpers\ModelResolver;
use App\Models\Invoice;
use App\Models\Product;

/**
 * Data transfer object for invoice - product details
 */
final readonly class InvoiceProductDto
{

    private function __construct(private readonly Invoice $invoice,
                                 private readonly Product $product,
                                 private readonly int     $productQty,
                                 private readonly float   $productUnitPrice)
    {
    }

    public static function from(Invoice|int|string $invoice,
                                Product|int|string $product,
                                int                $productQty,
                                float|string              $productUnitPrice): InvoiceProductDto
    {
        $invoice = ModelResolver::resolve($invoice, Invoice::class);
        $product = ModelResolver::resolve($product, Product::class);

        // ensure invoice or product is not null
        if (!$invoice || !$product) {
            throw new \InvalidArgumentException("Product or invoice cannot be null", 422);
        }

        if ($productQty < 1) {
            throw new \InvalidArgumentException("Product quantity cannot be less than 1!", 422);
        }

        if(is_string($productUnitPrice)){
            $productUnitPrice = (float)$productUnitPrice;
        }

        return new InvoiceProductDto(
            invoice: $invoice,
            product: $product,
            productQty: $productQty,
            productUnitPrice: $productUnitPrice
        );
    }

    public function invoice(): Invoice
    {
        return $this->invoice;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function productQty(): int
    {
        return $this->productQty;
    }

    public function productUnitPrice(): float
    {
        return $this->productUnitPrice;
    }
}

