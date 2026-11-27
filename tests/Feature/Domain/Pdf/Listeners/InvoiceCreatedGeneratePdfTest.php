<?php

namespace Tests\Feature\Domain\Pdf\Listeners;

use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Events\InvoiceCreated;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use App\Domain\Invoice\Services\InvoiceService;
use App\Domain\Invoice\Strategies\CreateInvoiceStrategyFactory;
use App\Domain\Pdf\Events\FileFailed;
use App\Domain\Pdf\Events\FileSaved;
use App\Domain\Pdf\Exceptions\CannotGeneratePdfException;
use App\Domain\Pdf\Exceptions\NoProductLinesForInvoicePdfException;
use App\Helpers\DispatcherInterface;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use League\Flysystem\FilesystemException;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Tests\TestCase;
use Tests\TestHelpers;

/**
 * The pdf step after an invoice is created. It uses the real pdf.invoice template and a fake invoices disk.
 */
class InvoiceCreatedGeneratePdfTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    private ?FileSaved $saved = null;

    private ?FileFailed $failed = null;

    private ?string $readOnlyDir = null;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('invoices');

        $this->dispatcher()->listen(FileSaved::class, function (FileSaved $event) {
            $this->saved = $event;
        });
        $this->dispatcher()->listen(FileFailed::class, function (FileFailed $event) {
            $this->failed = $event;
        });
    }

    protected function tearDown(): void
    {
        if ($this->readOnlyDir !== null && is_dir($this->readOnlyDir)) {
            chmod($this->readOnlyDir, 0755);
            rmdir($this->readOnlyDir);
        }

        parent::tearDown();
    }

    private function dispatcher(): DispatcherInterface
    {
        return $this->app->make(DispatcherInterface::class);
    }

    private function invoiceWithLine(): Invoice
    {
        $invoice = $this->createInvoice(total: 10.00);
        $product = Product::create(['product_name' => 'Pita', 'product_weight_g' => 120]);
        InvoiceProduct::create([
            'invoice_id' => $invoice->invoice_id,
            'product_id' => $product->product_id,
            'product_qty' => 4,
            'product_unit_price' => 2.50,
        ]);

        return $invoice;
    }

    private function dispatchCreated(Invoice $invoice): void
    {
        $this->dispatcher()->dispatch(new InvoiceCreated($invoice));
    }

    /**
     * Makes writes to the invoices disk fail for real: a directory nobody can write to
     */
    private function makeTheInvoicesDiskUnwritable(): void
    {
        if (function_exists('posix_geteuid') && posix_geteuid() === 0) {
            $this->markTestSkipped('root can write to a read-only directory');
        }

        $this->readOnlyDir = sys_get_temp_dir() . '/nutipita-readonly-' . uniqid();
        mkdir($this->readOnlyDir, 0555);

        config(['filesystems.disks.invoices.root' => $this->readOnlyDir]);
        Storage::forgetDisk('invoices');
    }

    private function useABrokenTemplate(): void
    {
        View::getFinder()->prependLocation(base_path('tests/Fixtures/broken_views'));
    }

    /**
     * Swaps the logger for one that records what it is given
     */
    private function recordLogs(): object
    {
        $logger = new class extends AbstractLogger {
            /** @var array<int, array{level: string, message: string, context: array}> */
            public array $records = [];

            public function log($level, \Stringable|string $message, array $context = []): void
            {
                $this->records[] = ['level' => $level, 'message' => (string)$message, 'context' => $context];
            }
        };

        $this->app->instance(LoggerInterface::class, $logger);

        return $logger;
    }

    /**
     * @return string[] the messages of an exception and of everything it was caused by
     */
    private function chainMessages(\Throwable $e): array
    {
        $messages = [];
        for (; $e !== null; $e = $e->getPrevious()) {
            $messages[] = $e->getMessage();
        }

        return $messages;
    }

    private function assertFailedWith(string $causeClass): void
    {
        $this->assertNotNull($this->failed, 'FileFailed was not dispatched');
        $this->assertInstanceOf(CannotGeneratePdfException::class, $this->failed->exception);
        $this->assertSame('Pdf generation failed', $this->failed->exception->getMessage());
        $this->assertInstanceOf($causeClass, $this->failed->exception->getPrevious());
        $this->assertNull($this->saved, 'FileSaved must not be dispatched when generation failed');
    }

    // ====
    // happy path
    // ====

    public function test_it_writes_a_real_pdf_to_the_invoices_disk_at_the_invoice_path(): void
    {
        $invoice = $this->invoiceWithLine();

        $this->dispatchCreated($invoice);

        Storage::disk('invoices')->assertExists($invoice->invoice_path);
        $this->assertStringStartsWith('%PDF', Storage::disk('invoices')->get($invoice->invoice_path));
    }

    public function test_it_announces_the_saved_file_and_nothing_failed(): void
    {
        $invoice = $this->invoiceWithLine();

        $this->dispatchCreated($invoice);

        $this->assertNotNull($this->saved);
        $this->assertSame($invoice->invoice_path, $this->saved->savedPath);
        $this->assertNull($this->failed);
    }

    public function test_it_only_writes_to_the_invoices_disk(): void
    {
        $invoice = $this->invoiceWithLine();

        $this->dispatchCreated($invoice);

        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_it_overwrites_a_file_that_already_exists(): void
    {
        $invoice = $this->invoiceWithLine();
        Storage::disk('invoices')->put($invoice->invoice_path, 'an old and broken file');

        $this->dispatchCreated($invoice);

        $this->assertStringStartsWith('%PDF', Storage::disk('invoices')->get($invoice->invoice_path));
    }

    public function test_the_saved_file_is_logged(): void
    {
        $logger = $this->recordLogs();
        $invoice = $this->invoiceWithLine();

        $this->dispatchCreated($invoice);

        $messages = implode("\n", array_column($logger->records, 'message'));
        $this->assertStringContainsString('New file saved to ' . $invoice->invoice_path, $messages);
    }

    // ====
    // failure path
    // ====

    public function test_an_invoice_without_lines_fails_and_writes_nothing(): void
    {
        $invoice = $this->createInvoice();

        $this->dispatchCreated($invoice);

        $this->assertFailedWith(NoProductLinesForInvoicePdfException::class);
        $this->assertSame([], Storage::disk('invoices')->allFiles());
    }

    public function test_an_invoice_that_no_longer_exists_fails(): void
    {
        $invoice = $this->invoiceWithLine();
        $invoice->delete();

        $this->dispatchCreated($invoice);

        $this->assertFailedWith(InvoiceNotFoundException::class);
    }

    public function test_a_failing_template_fails_and_keeps_the_reason(): void
    {
        $this->useABrokenTemplate();
        $invoice = $this->invoiceWithLine();

        $this->dispatchCreated($invoice);

        $this->assertNotNull($this->failed);
        $this->assertInstanceOf(CannotGeneratePdfException::class, $this->failed->exception);
        $this->assertContains('template exploded', $this->chainMessages($this->failed->exception));
        $this->assertSame([], Storage::disk('invoices')->allFiles());
        $this->assertNull($this->saved);
    }

    public function test_a_disk_that_cannot_be_written_fails_with_the_storage_error(): void
    {
        $this->makeTheInvoicesDiskUnwritable();
        $invoice = $this->invoiceWithLine();

        $this->dispatchCreated($invoice);

        $this->assertFailedWith(FilesystemException::class);
    }

    public function test_a_failure_does_not_throw_to_the_caller(): void
    {
        $invoice = $this->createInvoice(); // no lines

        $this->dispatchCreated($invoice);

        $this->assertNotNull($this->failed);
    }

    public function test_a_failing_listener_of_the_saved_file_is_not_reported_as_a_pdf_failure(): void
    {
        $this->dispatcher()->listen(FileSaved::class, function () {
            throw new \RuntimeException('something unrelated broke');
        });
        $invoice = $this->invoiceWithLine();

        try {
            $this->dispatchCreated($invoice);
            $this->fail('Expected the unrelated failure to propagate');
        } catch (\RuntimeException $e) {
            $this->assertSame('something unrelated broke', $e->getMessage());
        }

        $this->assertNull($this->failed, 'the pdf was saved, so nothing failed');
        Storage::disk('invoices')->assertExists($invoice->invoice_path);
    }

    // ====
    // logging the failure
    // ====

    public function test_a_failure_is_logged_at_error_level_with_the_exception_and_its_cause(): void
    {
        $logger = $this->recordLogs();
        $invoice = $this->createInvoice(); // no lines

        $this->dispatchCreated($invoice);

        $errors = array_values(array_filter($logger->records, fn($r) => $r['level'] === 'error'));
        $this->assertCount(1, $errors);
        $this->assertSame('Pdf generation failed', $errors[0]['message']);
        $this->assertInstanceOf(CannotGeneratePdfException::class, $errors[0]['context']['exception']);
        $this->assertInstanceOf(NoProductLinesForInvoicePdfException::class, $errors[0]['context']['exception']->getPrevious());
    }

    public function test_a_successful_generation_logs_no_error(): void
    {
        $logger = $this->recordLogs();

        $this->dispatchCreated($this->invoiceWithLine());

        $this->assertSame([], array_filter($logger->records, fn($r) => in_array($r['level'], ['warning', 'error'], true)));
    }

    // ====
    // creating an invoice when the pdf cannot be made
    // ====

    private function createThroughTheService(int $orderId): Invoice
    {
        return $this->app->make(InvoiceService::class)
            ->createRecordUsingStrategy($this->app->make(CreateInvoiceStrategyFactory::class)->single($orderId));
    }

    private function orderWithLines(): int
    {
        $order = $this->createOrder($this->createCustomer());
        $order->products()->attach($this->createProduct('Pita')->product_id, ['product_qty' => 4, 'order_product_unit_price' => 2.50]);

        return $order->order_id;
    }

    public function test_creating_an_invoice_succeeds_even_when_the_disk_cannot_be_written(): void
    {
        $this->makeTheInvoicesDiskUnwritable();
        $logger = $this->recordLogs();
        $orderId = $this->orderWithLines();

        $invoice = $this->createThroughTheService($orderId);

        $this->assertDatabaseHas('invoices', ['invoice_id' => $invoice->invoice_id]);
        $this->assertDatabaseHas('invoice_orders', ['invoice_id' => $invoice->invoice_id, 'order_id' => $orderId]);
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($orderId));
        $this->assertNotNull($this->failed);
        $this->assertNotNull(array_filter($logger->records, fn($r) => $r['level'] === 'error' && $r['message'] === 'Pdf generation failed'));
    }

    public function test_creating_an_invoice_succeeds_even_when_the_template_fails(): void
    {
        $this->useABrokenTemplate();
        $orderId = $this->orderWithLines();

        $invoice = $this->createThroughTheService($orderId);

        $this->assertDatabaseHas('invoices', ['invoice_id' => $invoice->invoice_id]);
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($orderId));
        $this->assertNotNull($this->failed);
        $this->assertSame([], Storage::disk('invoices')->allFiles());
    }

    public function test_creating_an_invoice_writes_its_pdf_to_the_invoices_disk(): void
    {
        $orderId = $this->orderWithLines();

        $invoice = $this->createThroughTheService($orderId);

        Storage::disk('invoices')->assertExists($invoice->invoice_path);
        $this->assertSame($invoice->invoice_path, $this->saved?->savedPath);
    }
}
