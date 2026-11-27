<?php

namespace Tests\Feature\Domain\Pdf\Services;

use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use App\Domain\Pdf\Entities\GeneratedPdf;
use App\Domain\Pdf\Entities\InvoicePdfData;
use App\Domain\Pdf\Exceptions\NoProductLinesForInvoicePdfException;
use App\Domain\Pdf\Services\InvoicePdfService;
use App\Models\CustomerProductPrice;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\TestHelpers;

/**
 * These tests use a stub pdf.invoice view (tests/Fixtures/views), so they cover what the service
 * builds and hands to the view, not how the real template looks.
 */
class InvoicePdfServiceTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    private InvoicePdfService $service;

    private ?InvoicePdfData $captured = null;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('invoices');
        View::getFinder()->prependLocation(base_path('tests/Fixtures/views'));
        View::composer('pdf.invoice', function ($view) {
            $this->captured = $view->getData()['data'] ?? null;
        });

        $this->service = $this->app->make(InvoicePdfService::class);
    }

    private function product(string $name, ?int $weight = null): Product
    {
        return Product::create(['product_name' => $name, 'product_weight_g' => $weight]);
    }

    private function addLine(Invoice $invoice, Product $product, int $qty, float $unitPrice): void
    {
        InvoiceProduct::create([
            'invoice_id' => $invoice->invoice_id,
            'product_id' => $product->product_id,
            'product_qty' => $qty,
            'product_unit_price' => $unitPrice,
        ]);
    }

    /**
     * An invoice with one line of 4 x 2.50 = 10.00, stored total 10.00
     */
    private function invoiceWithLine(float $total = 10.00): Invoice
    {
        $invoice = $this->createInvoice(total: $total);
        $invoice->update(['invoice_name' => 'INV-0042.pdf']);
        $this->addLine($invoice, $this->product('Pita', 120), 4, 2.50);

        return $invoice;
    }

    private function dataFor(Invoice $invoice): InvoicePdfData
    {
        $this->service->forId($invoice->invoice_id);

        $this->assertNotNull($this->captured, 'the view was never given any data');

        return $this->captured;
    }

    // ====
    // failure path
    // ====

    public function test_it_throws_invoice_not_found_for_an_unknown_id(): void
    {
        $this->expectException(InvoiceNotFoundException::class);
        $this->expectExceptionMessage('999999');

        $this->service->forId(999999);
    }

    public function test_it_refuses_an_invoice_without_product_lines(): void
    {
        $invoice = $this->createInvoice();

        try {
            $this->service->forId($invoice->invoice_id);
            $this->fail('Expected NoProductLinesForInvoicePdfException');
        } catch (NoProductLinesForInvoicePdfException $e) {
            $this->assertStringContainsString($invoice->invoice_number, $e->getMessage());
        }
    }

    public function test_it_does_not_reach_the_view_when_the_invoice_has_no_lines(): void
    {
        $invoice = $this->createInvoice();

        try {
            $this->service->forId($invoice->invoice_id);
        } catch (NoProductLinesForInvoicePdfException) {
        }

        $this->assertNull($this->captured);
    }

    public function test_an_invoice_with_lines_is_not_refused(): void
    {
        $invoice = $this->invoiceWithLine();

        $this->assertInstanceOf(GeneratedPdf::class, $this->service->forId($invoice->invoice_id));
    }

    // ====
    // the returned pdf
    // ====

    public function test_it_returns_a_real_pdf_named_after_the_invoice(): void
    {
        $invoice = $this->invoiceWithLine();

        $response = $this->service->forId($invoice->invoice_id)->download();

        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('filename=INV-0042.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_it_does_not_write_any_file(): void
    {
        $invoice = $this->invoiceWithLine();

        $this->service->forId($invoice->invoice_id);

        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_it_does_not_change_the_invoice(): void
    {
        $invoice = $this->invoiceWithLine();
        $before = (array)DB::table('invoices')->where('invoice_id', $invoice->invoice_id)->first();

        $this->service->forId($invoice->invoice_id);

        $this->assertSame($before, (array)DB::table('invoices')->where('invoice_id', $invoice->invoice_id)->first());
    }

    // ====
    // the data handed to the view
    // ====

    public function test_it_hands_the_view_the_invoice_and_its_customer(): void
    {
        $customer = $this->createCustomer('Al Zayt');
        $invoice = $this->createInvoice(customer: $customer);
        $this->addLine($invoice, $this->product('Pita', 120), 1, 1.00);

        $data = $this->dataFor($invoice);

        $this->assertSame($invoice->invoice_id, $data->invoice->invoice_id);
        $this->assertSame($customer->customer_id, $data->customer->customer_id);
        $this->assertSame('Al Zayt', $data->customer->customer_name);
    }

    public function test_each_line_carries_the_saved_quantity_price_and_total(): void
    {
        $invoice = $this->createInvoice(total: 26.25);
        $pita = $this->product('Pita', 120);
        $this->addLine($invoice, $pita, 75, 0.35);

        $data = $this->dataFor($invoice);

        $this->assertCount(1, $data->products);
        $line = $data->products[0];
        $this->assertSame($pita->product_id, $line->product->product_id);
        $this->assertSame(75, $line->quantity);
        $this->assertEqualsWithDelta(0.35, $line->unitPrice, 0.00001);
        $this->assertEqualsWithDelta(26.25, $line->productTotal, 0.00001);
    }

    public function test_it_includes_every_line_of_the_invoice_and_only_those(): void
    {
        $invoice = $this->createInvoice();
        $other = $this->createInvoice();
        $a = $this->product('Pita', 120);
        $b = $this->product('Bagel', 90);
        $this->addLine($invoice, $a, 1, 1.00);
        $this->addLine($invoice, $b, 2, 1.00);
        $this->addLine($other, $this->product('Roll', 60), 9, 9.00);

        $data = $this->dataFor($invoice);

        $this->assertEqualsCanonicalizing(
            [$a->product_id, $b->product_id],
            array_map(fn($line) => $line->product->product_id, $data->products)
        );
    }

    public function test_lines_are_ordered_by_quantity_then_name_then_weight(): void
    {
        $invoice = $this->createInvoice();
        $cake = $this->product('Cake', 90);
        $roll = $this->product('Zaatar roll', null);
        $pitaSmall = $this->product('Pita', 60);
        $pitaLarge = $this->product('Pita', 120);
        $bagel = $this->product('Bagel', 120);
        $pitaBulk = $this->product('Pita', 90);
        // inserted in a scrambled order
        $this->addLine($invoice, $cake, 2, 1.00);
        $this->addLine($invoice, $pitaSmall, 100, 1.00);
        $this->addLine($invoice, $roll, 7, 1.00);
        $this->addLine($invoice, $pitaBulk, 500, 1.00);
        $this->addLine($invoice, $bagel, 100, 1.00);
        $this->addLine($invoice, $pitaLarge, 100, 1.00);

        $data = $this->dataFor($invoice);

        // quantity descending, then name ascending, then weight descending
        $this->assertSame(
            [$pitaBulk->product_id, $bagel->product_id, $pitaLarge->product_id, $pitaSmall->product_id, $roll->product_id, $cake->product_id],
            array_map(fn($line) => $line->product->product_id, $data->products)
        );
    }

    public function test_prices_come_from_the_invoice_not_from_the_current_customer_price(): void
    {
        $customer = $this->createCustomer();
        $invoice = $this->createInvoice(customer: $customer);
        $pita = $this->product('Pita', 120);
        $this->addLine($invoice, $pita, 10, 1.00);
        CustomerProductPrice::create([
            'customer_id' => $customer->customer_id,
            'product_id' => $pita->product_id,
            'customer_product_price' => 9.99,
        ]);

        $data = $this->dataFor($invoice);

        $this->assertEqualsWithDelta(1.00, $data->products[0]->unitPrice, 0.00001);
        $this->assertEqualsWithDelta(10.00, $data->products[0]->productTotal, 0.00001);
    }

    public function test_generating_two_invoices_does_not_mix_their_data(): void
    {
        $first = $this->invoiceWithLine();
        $second = $this->createInvoice(total: 55.00);
        $this->addLine($second, $this->product('Bagel', 90), 11, 5.00);

        $dataFirst = $this->dataFor($first);
        $dataSecond = $this->dataFor($second);

        $this->assertSame($first->invoice_id, $dataFirst->invoice->invoice_id);
        $this->assertSame($second->invoice_id, $dataSecond->invoice->invoice_id);
        $this->assertCount(1, $dataSecond->products);
        $this->assertSame(11, $dataSecond->products[0]->quantity);
    }

    // ====
    // totals and credit
    // ====

    public function test_the_subtotal_is_the_stored_invoice_total(): void
    {
        $invoice = $this->invoiceWithLine(total: 10.00);

        $data = $this->dataFor($invoice);

        $this->assertEqualsWithDelta(10.00, $data->invoiceTotal->subtotal(), 0.001);
    }

    public function test_the_stored_total_is_used_as_it_is_and_not_recalculated_from_the_lines(): void
    {
        // the stored total already includes the delivery charge, so it may legitimately differ from the lines
        $invoice = $this->createInvoice(total: 15.00);
        $invoice->update(['invoice_delivery_charge' => '5']);
        $this->addLine($invoice, $this->product('Pita', 120), 4, 2.50); // lines add up to 10.00

        $data = $this->dataFor($invoice);

        $this->assertEqualsWithDelta(15.00, $data->invoiceTotal->subtotal(), 0.001);
        $this->assertEqualsWithDelta(15.00, $data->invoiceTotal->total(), 0.001);
        $this->assertEqualsWithDelta(5.0, $data->invoice->getDeliveryCharge()->getCalculated(), 0.001);
    }

    public function test_without_a_credit_the_total_equals_the_subtotal(): void
    {
        $invoice = $this->invoiceWithLine(total: 10.00);

        $data = $this->dataFor($invoice);

        $this->assertEqualsWithDelta(10.00, $data->invoiceTotal->total(), 0.001);
    }

    public function test_a_credit_is_taken_off_the_total_but_not_the_subtotal(): void
    {
        $invoice = $this->invoiceWithLine(total: 10.00);
        $invoice->update(['invoice_credit' => 3.00]);

        $data = $this->dataFor($invoice);

        $this->assertEqualsWithDelta(10.00, $data->invoiceTotal->subtotal(), 0.001);
        $this->assertEqualsWithDelta(7.00, $data->invoiceTotal->total(), 0.001);
    }

    public function test_a_zero_credit_changes_nothing(): void
    {
        $invoice = $this->invoiceWithLine(total: 10.00);
        $invoice->update(['invoice_credit' => 0]);

        $data = $this->dataFor($invoice);

        $this->assertEqualsWithDelta(10.00, $data->invoiceTotal->total(), 0.001);
    }

    public function test_a_credit_with_decimals_is_applied_exactly(): void
    {
        $invoice = $this->invoiceWithLine(total: 8962.00);
        $invoice->update(['invoice_credit' => 243.56]);

        $data = $this->dataFor($invoice);

        $this->assertEqualsWithDelta(8718.44, $data->invoiceTotal->total(), 0.001);
    }

    public function test_a_credit_larger_than_the_total_gives_a_negative_total(): void
    {
        // documents today's behaviour: nothing stops it, the amount simply goes below zero
        $invoice = $this->invoiceWithLine(total: 10.00);
        $invoice->update(['invoice_credit' => 50.00]);

        $data = $this->dataFor($invoice);

        $this->assertEqualsWithDelta(-40.00, $data->invoiceTotal->total(), 0.001);
    }

    /**
     * @return array<string, array{?float, float}>
     */
    public static function creditsAndExpectedTotals(): array
    {
        return [
            'no credit' => [null, 100.00],
            'small credit' => [0.01, 99.99],
            'half' => [50.00, 50.00],
            'exactly the total' => [100.00, 0.00],
        ];
    }

    #[DataProvider('creditsAndExpectedTotals')]
    public function test_the_total_for_a_range_of_credits(?float $credit, float $expected): void
    {
        $invoice = $this->invoiceWithLine(total: 100.00);
        $invoice->update(['invoice_credit' => $credit]);

        $data = $this->dataFor($invoice);

        $this->assertEqualsWithDelta($expected, $data->invoiceTotal->total(), 0.001);
    }
}
