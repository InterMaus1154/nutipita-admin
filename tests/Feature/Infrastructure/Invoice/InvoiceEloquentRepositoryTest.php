<?php

namespace Tests\Feature\Infrastructure\Invoice;

use App\Enums\InvoiceStatus;
use App\Infrastructure\Invoice\InvoiceEloquentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers;

class InvoiceEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    private InvoiceEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(InvoiceEloquentRepository::class);
    }

    public function test_all_unpaid_sum_returns_sum_of_due_invoices_only(): void
    {
        $this->createInvoice(InvoiceStatus::due, 100.50);
        $this->createInvoice(InvoiceStatus::due, 49.50);
        $this->createInvoice(InvoiceStatus::paid, 200.00);
        $this->createInvoice(InvoiceStatus::cancelled, 75.00);

        $sum = $this->repository->allUnpaidSum();

        $this->assertEquals(150.00, $sum);
    }

    public function test_all_unpaid_sum_returns_zero_when_no_due_invoices_exist(): void
    {
        $this->createInvoice(InvoiceStatus::paid, 200.00);
        $this->createInvoice(InvoiceStatus::cancelled, 75.00);

        $sum = $this->repository->allUnpaidSum();

        $this->assertEquals(0, $sum);
    }
}
