<?php

namespace Tests\Feature\Infrastructure\Invoice;

use App\Domain\Invoice\InvoiceStatus;
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
        $this->createInvoice(InvoiceStatus::UNPAID, 100.50);
        $this->createInvoice(InvoiceStatus::UNPAID, 49.50);
        $this->createInvoice(InvoiceStatus::PAID, 200.00);
        $this->createInvoice(InvoiceStatus::CANCELLED, 75.00);

        $sum = $this->repository->allUnpaidSum();

        $this->assertEquals(150.00, $sum);
    }

    public function test_all_unpaid_sum_returns_zero_when_no_due_invoices_exist(): void
    {
        $this->createInvoice(InvoiceStatus::PAID, 200.00);
        $this->createInvoice(InvoiceStatus::CANCELLED, 75.00);

        $sum = $this->repository->allUnpaidSum();

        $this->assertEquals(0, $sum);
    }
}
