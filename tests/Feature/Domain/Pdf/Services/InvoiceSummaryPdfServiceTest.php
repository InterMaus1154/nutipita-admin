<?php

namespace Tests\Feature\Domain\Pdf\Services;

use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use App\Domain\Pdf\Entities\GeneratedPdf;
use App\Domain\Pdf\Entities\InvoiceSummaryData;
use App\Domain\Pdf\Enums\InvoiceSummaryDisplayMode;
use App\Domain\Pdf\Exceptions\NoItemsForInvoiceSummaryException;
use App\Domain\Pdf\Services\InvoiceSummaryPdfService;
use App\Models\InvoiceProduct;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Tests\TestCase;
use Tests\TestHelpers;

/**
 * These tests use the real pdf.invoice-summary template, so a data class the template can't read
 * (e.g. a property that isn't public) fails here. The view composer only captures what the service hands over.
 */
class InvoiceSummaryPdfServiceTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    private InvoiceSummaryPdfService $service;

    private ?InvoiceSummaryData $captured = null;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('invoices');
        View::composer('pdf.invoice-summary', function ($view) {
            $this->captured = $view->getData()['data'] ?? null;
        });

        $this->service = $this->app->make(InvoiceSummaryPdfService::class);
    }

    private function product(string $name, ?int $weight = null): Product
    {
        return Product::create(['product_name' => $name, 'product_weight_g' => $weight]);
    }

    private function addOrderLine(Order $order, Product $product, int $qty, float $unitPrice): void
    {
        DB::table('order_product')->insert([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'product_qty' => $qty,
            'order_product_unit_price' => $unitPrice,
        ]);
    }

    private function attach(Invoice $invoice, Order ...$orders): void
    {
        $invoice->orders()->attach(array_map(fn(Order $o) => $o->order_id, $orders));
    }

    private function generate(Invoice $invoice): InvoiceSummaryData
    {
        $pdf = $this->service->forId($invoice->invoice_id);

        $this->assertInstanceOf(GeneratedPdf::class, $pdf);
        $this->assertNotNull($this->captured, 'the view was never given any data');

        return $this->captured;
    }

    public function test_it_throws_invoice_not_found_for_an_unknown_id(): void
    {
        $this->expectException(InvoiceNotFoundException::class);

        $this->service->forId(999999);
    }

    public function test_it_refuses_an_invoice_with_neither_orders_nor_products(): void
    {
        $invoice = $this->createInvoice();

        $this->expectException(NoItemsForInvoiceSummaryException::class);

        $this->service->forId($invoice->invoice_id);
    }

    public function test_it_renders_orders_mode_sorted_by_due_date_with_pivot_prices(): void
    {
        $customer = $this->createCustomer();
        $invoice = $this->createInvoice(customer: $customer);
        $pita = $this->product('Brown Pita', 100);
        $wrap = $this->product('Wrap');

        $late = $this->createOrder($customer, dueDate: '2026-03-20');
        $early = $this->createOrder($customer, dueDate: '2026-03-05');
        $this->addOrderLine($late, $pita, 10, 2.00);
        $this->addOrderLine($early, $pita, 4, 1.50);
        $this->addOrderLine($early, $wrap, 2, 3.00);
        $this->attach($invoice, $late, $early);

        $data = $this->generate($invoice);

        $this->assertSame(InvoiceSummaryDisplayMode::RENDER_ORDERS, $data->displayMode);
        $this->assertSame(
            ['2026-03-05', '2026-03-20'],
            array_map(fn($o) => $o->due->toDateString(), $data->orderData),
        );

        $earlyLines = $data->orderData[0]->products;
        $this->assertCount(2, $earlyLines);
        $pitaLine = collect($earlyLines)->first(fn($l) => $l->product->product_id === $pita->product_id);
        $this->assertSame(4, $pitaLine->qty);
        $this->assertEqualsWithDelta(1.50, $pitaLine->unitPrice, 0.0001);
        $this->assertEqualsWithDelta(6.00, $pitaLine->total, 0.0001);
    }

    public function test_it_falls_back_to_products_mode_for_an_invoice_without_orders(): void
    {
        $invoice = $this->createInvoice(total: 10.00);
        InvoiceProduct::create([
            'invoice_id' => $invoice->invoice_id,
            'product_id' => $this->product('Pita', 120)->product_id,
            'product_qty' => 4,
            'product_unit_price' => 2.50,
        ]);

        $data = $this->generate($invoice);

        $this->assertSame(InvoiceSummaryDisplayMode::RENDER_PRODUCTS, $data->displayMode);
        $this->assertCount(1, $data->productData);
        $this->assertSame(4, $data->productData[0]->qty);
        $this->assertEqualsWithDelta(10.00, $data->productData[0]->total, 0.0001);
    }
}
