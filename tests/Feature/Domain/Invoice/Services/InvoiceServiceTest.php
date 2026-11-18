<?php

namespace Tests\Feature\Domain\Invoice\Services;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Domain\Invoice\Exceptions\CannotUpdateInvoiceStatusException;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use App\Domain\Invoice\Services\InvoiceService;
use App\Enums\OrderStatus;
use App\Infrastructure\Invoice\InvoiceEloquentRepository;
use App\Models\CreditNote;
use Illuminate\Support\Facades\DB;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\TestHelpers;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    private InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->service = $this->app->make(InvoiceService::class);
    }

    // ====
    // delete
    // ====

    public function test_delete_removes_the_invoice_and_its_pivot_rows(): void
    {
        $invoice = $this->createInvoice();
        $order = $this->createOrder($invoice->customer_id);
        $invoice->orders()->attach($order->order_id);

        $this->service->delete($invoice->invoice_id);

        $this->assertDatabaseMissing('invoices', ['invoice_id' => $invoice->invoice_id]);
        $this->assertDatabaseMissing('invoice_orders', ['invoice_id' => $invoice->invoice_id]);
    }

    public function test_delete_sets_every_order_of_the_invoice_back_to_confirmed(): void
    {
        $invoice = $this->createInvoice();
        $paid = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $unpaid = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach([$paid->order_id, $unpaid->order_id]);

        $this->service->delete($invoice->invoice_id);

        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($paid));
        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($unpaid));
    }

    public function test_delete_keeps_the_orders_themselves(): void
    {
        $invoice = $this->createInvoice();
        $order = $this->createOrder($invoice->customer_id);
        $invoice->orders()->attach($order->order_id);

        $this->service->delete($invoice->invoice_id);

        $this->assertDatabaseHas('orders', ['order_id' => $order->order_id]);
    }

    public function test_delete_does_not_touch_orders_that_are_not_on_the_invoice(): void
    {
        $invoice = $this->createInvoice();
        $onInvoice = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $sameCustomerOther = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $otherCustomer = $this->createOrder(status: OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach($onInvoice->order_id);

        $this->service->delete($invoice->invoice_id);

        $this->assertSame('G_PAID', $this->storedOrderStatus($sameCustomerOther));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($otherCustomer));
    }

    public function test_delete_does_not_touch_other_invoices_or_their_orders(): void
    {
        $gone = $this->createInvoice();
        $goneOrder = $this->createOrder($gone->customer_id);
        $gone->orders()->attach($goneOrder->order_id);

        $kept = $this->createInvoice();
        $keptOrder = $this->createOrder($kept->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $kept->orders()->attach($keptOrder->order_id);

        $this->service->delete($gone->invoice_id);

        $this->assertDatabaseHas('invoices', ['invoice_id' => $kept->invoice_id]);
        $this->assertDatabaseHas('invoice_orders', ['invoice_id' => $kept->invoice_id, 'order_id' => $keptOrder->order_id]);
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($keptOrder));
    }

    public function test_delete_removes_the_pdf_file(): void
    {
        $invoice = $this->createInvoice();
        Storage::disk('local')->put($invoice->invoice_path, 'pdf');

        $this->service->delete($invoice->invoice_id);

        Storage::disk('local')->assertMissing($invoice->invoice_path);
    }

    public function test_delete_does_not_remove_the_pdf_of_other_invoices(): void
    {
        $gone = $this->createInvoice();
        $kept = $this->createInvoice();
        Storage::disk('local')->put($gone->invoice_path, 'pdf');
        Storage::disk('local')->put('invoices/other.pdf', 'pdf');
        $kept->update(['invoice_path' => 'invoices/other.pdf']);

        $this->service->delete($gone->invoice_id);

        Storage::disk('local')->assertExists('invoices/other.pdf');
    }

    public function test_delete_works_for_an_invoice_without_orders_or_a_pdf_file(): void
    {
        $invoice = $this->createInvoice();

        $this->service->delete($invoice->invoice_id);

        $this->assertDatabaseMissing('invoices', ['invoice_id' => $invoice->invoice_id]);
    }

    public function test_delete_throws_invoice_not_found_for_an_unknown_id(): void
    {
        $this->expectException(InvoiceNotFoundException::class);
        $this->expectExceptionMessage('999999');

        $this->service->delete(999999);
    }

    public function test_delete_of_an_unknown_id_changes_nothing(): void
    {
        $invoice = $this->createInvoice();
        $order = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $invoice->orders()->attach($order->order_id);

        try {
            $this->service->delete(999999);
            $this->fail('Expected InvoiceNotFoundException');
        } catch (InvoiceNotFoundException) {
        }

        $this->assertDatabaseHas('invoices', ['invoice_id' => $invoice->invoice_id]);
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    public function test_delete_reports_the_exception_it_throws(): void
    {
        Exceptions::fake();

        try {
            $this->service->delete(999999);
        } catch (InvoiceNotFoundException) {
        }

        Exceptions::assertReported(InvoiceNotFoundException::class);
    }

    public function test_delete_rolls_everything_back_when_the_invoice_cannot_be_deleted(): void
    {
        // credit_notes.invoice_id has no cascade, so the database refuses to delete an invoice that has one
        $invoice = $this->createInvoice();
        $order = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $invoice->orders()->attach($order->order_id);
        Storage::disk('local')->put($invoice->invoice_path, 'pdf');
        CreditNote::create([
            'invoice_id' => $invoice->invoice_id,
            'credit_note_issued_at' => '2026-03-10',
            'credit_note_number' => 'CN-0001',
        ]);

        try {
            $this->service->delete($invoice->invoice_id);
            $this->fail('Expected the database to refuse the delete');
        } catch (QueryException) {
        }

        $this->assertDatabaseHas('invoices', ['invoice_id' => $invoice->invoice_id]);
        $this->assertDatabaseHas('invoice_orders', ['invoice_id' => $invoice->invoice_id, 'order_id' => $order->order_id]);
        $this->assertSame('G_PAID', $this->storedOrderStatus($order), 'the order status update must be rolled back');
        Storage::disk('local')->assertExists($invoice->invoice_path);
    }

    // ====
    // updateStatus
    // ====

    private function storedInvoiceStatus(int $invoiceId): int
    {
        return (int)DB::table('invoices')->where('invoice_id', $invoiceId)->value('invoice_status_new');
    }

    /**
     * @return array<string, array{InvoiceStatus, OrderStatus}>
     */
    public static function statusToOrderStatusProvider(): array
    {
        return [
            'paid' => [InvoiceStatus::PAID, OrderStatus::G_PAID],
            'unpaid' => [InvoiceStatus::UNPAID, OrderStatus::O_DELIVERED_UNPAID],
            'loss' => [InvoiceStatus::LOSS, OrderStatus::O_DELIVERED_UNPAID],
            'cancelled' => [InvoiceStatus::CANCELLED, OrderStatus::Y_CONFIRMED],
        ];
    }

    #[DataProvider('statusToOrderStatusProvider')]
    public function test_update_status_updates_the_invoice_and_maps_the_order_status(InvoiceStatus $status, OrderStatus $expectedOrderStatus): void
    {
        // start from a status different from the expected one so every case is a real change
        $startStatus = $expectedOrderStatus === OrderStatus::G_PAID ? OrderStatus::Y_CONFIRMED : OrderStatus::G_PAID;
        $invoice = $this->createInvoice($status === InvoiceStatus::PAID ? InvoiceStatus::UNPAID : InvoiceStatus::PAID);
        $first = $this->createOrder($invoice->customer_id, $startStatus);
        $second = $this->createOrder($invoice->customer_id, $startStatus);
        $invoice->orders()->attach([$first->order_id, $second->order_id]);

        $this->service->updateStatus($invoice->invoice_id, $status);

        $this->assertSame($status->value, $this->storedInvoiceStatus($invoice->invoice_id));
        $this->assertSame($expectedOrderStatus->name, $this->storedOrderStatus($first));
        $this->assertSame($expectedOrderStatus->name, $this->storedOrderStatus($second));
    }

    public function test_update_status_does_not_touch_orders_that_are_not_on_the_invoice(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);
        $onInvoice = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $sameCustomerOther = $this->createOrder($invoice->customer_id, OrderStatus::Y_CONFIRMED);
        $otherCustomer = $this->createOrder(status: OrderStatus::Y_CONFIRMED);
        $invoice->orders()->attach($onInvoice->order_id);

        $this->service->updateStatus($invoice->invoice_id, InvoiceStatus::PAID);

        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($sameCustomerOther));
        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($otherCustomer));
    }

    public function test_update_status_does_not_touch_other_invoices(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);
        $other = $this->createInvoice(InvoiceStatus::UNPAID);
        $otherOrder = $this->createOrder($other->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $other->orders()->attach($otherOrder->order_id);

        $this->service->updateStatus($invoice->invoice_id, InvoiceStatus::PAID);

        $this->assertSame(InvoiceStatus::UNPAID->value, $this->storedInvoiceStatus($other->invoice_id));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($otherOrder));
    }

    public function test_update_status_works_for_an_invoice_without_orders(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);

        $this->service->updateStatus($invoice->invoice_id, InvoiceStatus::PAID);

        $this->assertSame(InvoiceStatus::PAID->value, $this->storedInvoiceStatus($invoice->invoice_id));
    }

    public function test_update_status_throws_cannot_update_for_an_unknown_id_and_keeps_the_cause(): void
    {
        try {
            $this->service->updateStatus(999999, InvoiceStatus::PAID);
            $this->fail('Expected CannotUpdateInvoiceStatusException');
        } catch (CannotUpdateInvoiceStatusException $e) {
            $this->assertStringContainsString('999999', $e->getMessage());
            $this->assertInstanceOf(InvoiceNotFoundException::class, $e->getPrevious());
        }
    }

    public function test_update_status_of_an_unknown_id_changes_nothing(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);
        $order = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach($order->order_id);

        try {
            $this->service->updateStatus(999999, InvoiceStatus::PAID);
        } catch (CannotUpdateInvoiceStatusException) {
        }

        $this->assertSame(InvoiceStatus::UNPAID->value, $this->storedInvoiceStatus($invoice->invoice_id));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($order));
    }

    public function test_update_status_rolls_back_the_order_update_when_saving_the_invoice_fails(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);
        $order = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach($order->order_id);

        // orders are updated first, then the commit of the invoice blows up
        $failing = Mockery::mock(InvoiceEloquentRepository::class)->makePartial();
        $failing->shouldReceive('commit')->once()->andThrow(new \RuntimeException('boom'));
        $this->app->instance(InvoiceRepository::class, $failing);
        $levelBefore = DB::transactionLevel();
        $service = $this->app->make(InvoiceService::class);

        try {
            $service->updateStatus($invoice->invoice_id, InvoiceStatus::PAID);
            $this->fail('Expected CannotUpdateInvoiceStatusException');
        } catch (CannotUpdateInvoiceStatusException $e) {
            $this->assertSame('boom', $e->getPrevious()->getMessage());
        }

        $this->assertSame(InvoiceStatus::UNPAID->value, $this->storedInvoiceStatus($invoice->invoice_id));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($order), 'the order status update must be rolled back');
        $this->assertSame($levelBefore, DB::transactionLevel(), 'the transaction must be closed');
    }
}
