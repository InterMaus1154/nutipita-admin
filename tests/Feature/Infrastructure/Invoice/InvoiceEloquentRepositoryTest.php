<?php

namespace Tests\Feature\Infrastructure\Invoice;

use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Domain\Invoice\Querying\InvoiceFilters;
use App\Domain\Invoice\Querying\InvoiceQuery;
use App\Helpers\SortOrder;
use App\Infrastructure\Invoice\InvoiceEloquentRepository;
use App\Models\InvoiceProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
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

    // ====
    // allUnpaidSum
    // ====

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

    // ====
    // findById
    // ====

    public function test_find_by_id_returns_invoice_with_customer_and_products_loaded(): void
    {
        $invoice = $this->createInvoice();
        $product = $this->createProduct('Pita Bread');
        InvoiceProduct::create([
            'invoice_id' => $invoice->invoice_id,
            'product_id' => $product->product_id,
            'product_qty' => 10,
            'product_unit_price' => 0.35,
        ]);

        $found = $this->repository->findById($invoice->invoice_id);

        $this->assertNotNull($found);
        $this->assertSame($invoice->invoice_id, $found->invoice_id);
        $this->assertTrue($found->relationLoaded('customer'));
        $this->assertTrue($found->relationLoaded('products'));
        $this->assertCount(1, $found->products);
        $this->assertTrue($found->products->first()->relationLoaded('product'));
        $this->assertSame('Pita Bread', $found->products->first()->product->product_name);
    }

    public function test_find_by_id_returns_null_when_invoice_does_not_exist(): void
    {
        $this->assertNull($this->repository->findById(999999));
    }

    public function test_find_by_id_loads_the_orders_attached_through_the_pivot(): void
    {
        $invoice = $this->createInvoice();
        $a = $this->createOrder($invoice->customer_id);
        $b = $this->createOrder($invoice->customer_id);
        $this->createOrder($invoice->customer_id); // not on the invoice
        $invoice->orders()->attach([$a->order_id, $b->order_id]);

        $found = $this->repository->findById($invoice->invoice_id);

        $this->assertTrue($found->relationLoaded('orders'));
        $this->assertEqualsCanonicalizing([$a->order_id, $b->order_id], $found->orders->pluck('order_id')->all());
    }

    public function test_find_by_id_does_not_return_other_invoices(): void
    {
        $wanted = $this->createInvoice();
        $this->createInvoice();
        $this->createInvoice();

        $found = $this->repository->findById($wanted->invoice_id);

        $this->assertSame($wanted->invoice_id, $found->invoice_id);
    }

    // ====
    // delete
    // ====

    public function test_delete_removes_the_invoice_and_returns_true(): void
    {
        $invoice = $this->createInvoice();

        $this->assertTrue($this->repository->delete($invoice->invoice_id));
        $this->assertDatabaseMissing('invoices', ['invoice_id' => $invoice->invoice_id]);
    }

    public function test_delete_returns_false_when_the_invoice_does_not_exist(): void
    {
        $this->createInvoice();

        $this->assertFalse($this->repository->delete(999999));
        $this->assertDatabaseCount('invoices', 1);
    }

    public function test_delete_leaves_other_invoices_untouched(): void
    {
        $gone = $this->createInvoice();
        $kept = $this->createInvoice();

        $this->repository->delete($gone->invoice_id);

        $this->assertDatabaseHas('invoices', ['invoice_id' => $kept->invoice_id]);
        $this->assertDatabaseCount('invoices', 1);
    }

    public function test_delete_removes_the_pivot_rows_and_invoice_products_but_keeps_the_orders(): void
    {
        $invoice = $this->createInvoice();
        $order = $this->createOrder($invoice->customer_id);
        $invoice->orders()->attach($order->order_id);
        $product = $this->createProduct();
        InvoiceProduct::create([
            'invoice_id' => $invoice->invoice_id,
            'product_id' => $product->product_id,
            'product_qty' => 3,
            'product_unit_price' => 0.35,
        ]);

        $this->repository->delete($invoice->invoice_id);

        $this->assertDatabaseMissing('invoice_orders', ['invoice_id' => $invoice->invoice_id]);
        $this->assertDatabaseMissing('invoice_products', ['invoice_id' => $invoice->invoice_id]);
        $this->assertDatabaseHas('orders', ['order_id' => $order->order_id]);
        $this->assertDatabaseHas('products', ['product_id' => $product->product_id]);
    }

    // ====
    // commit
    // ====

    public function test_commit_persists_changes_and_returns_the_same_invoice(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID, 100.00);

        $invoice->invoice_status_new = InvoiceStatus::PAID;
        $invoice->invoice_total = 250.00;
        $committed = $this->repository->commit($invoice);

        $this->assertSame($invoice, $committed);
        $this->assertDatabaseHas('invoices', [
            'invoice_id' => $invoice->invoice_id,
            'invoice_status_new' => InvoiceStatus::PAID->value,
            'invoice_total' => 250.00,
        ]);
    }

    // ====
    // find
    // ====

    public function test_find_returns_all_invoices_without_filters(): void
    {
        $this->createInvoice();
        $this->createInvoice();
        $this->createInvoice();

        $this->assertCount(3, $this->repository->find(new InvoiceQuery()));
    }

    public function test_find_filters_by_customer(): void
    {
        $customer = $this->createCustomer();
        $mine = $this->createInvoice(customer: $customer);
        $this->createInvoice();

        $found = $this->repository->find((new InvoiceQuery())->customerId($customer->customer_id));

        $this->assertCount(1, $found);
        $this->assertSame($mine->invoice_id, $found->first()->invoice_id);
    }

    public function test_find_filters_by_due_date_range_inclusive(): void
    {
        $this->createInvoice(dueDate: '2026-02-28'); // before the range
        $first = $this->createInvoice(dueDate: '2026-03-01');
        $middle = $this->createInvoice(dueDate: '2026-03-15');
        $last = $this->createInvoice(dueDate: '2026-03-31');
        $this->createInvoice(dueDate: '2026-04-01'); // after the range

        $found = $this->repository->find(
            (new InvoiceQuery())->invoiceDueFrom('2026-03-01')->invoiceDueTo('2026-03-31')
        );

        $this->assertEqualsCanonicalizing(
            [$first->invoice_id, $middle->invoice_id, $last->invoice_id],
            $found->pluck('invoice_id')->all()
        );
    }

    public function test_find_filters_by_due_from_only(): void
    {
        $this->createInvoice(dueDate: '2026-02-28');
        $on = $this->createInvoice(dueDate: '2026-03-01');
        $later = $this->createInvoice(dueDate: '2026-06-01');

        $found = $this->repository->find((new InvoiceQuery())->invoiceDueFrom('2026-03-01'));

        $this->assertEqualsCanonicalizing(
            [$on->invoice_id, $later->invoice_id],
            $found->pluck('invoice_id')->all()
        );
    }

    public function test_find_filters_by_due_to_only(): void
    {
        $earlier = $this->createInvoice(dueDate: '2026-01-01');
        $on = $this->createInvoice(dueDate: '2026-03-31');
        $this->createInvoice(dueDate: '2026-04-01');

        $found = $this->repository->find((new InvoiceQuery())->invoiceDueTo('2026-03-31'));

        $this->assertEqualsCanonicalizing(
            [$earlier->invoice_id, $on->invoice_id],
            $found->pluck('invoice_id')->all()
        );
    }

    public function test_find_eager_loads_requested_relationships(): void
    {
        $this->createInvoice();

        $invoice = $this->repository->find((new InvoiceQuery())->relationships(['customer', 'orders']))->first();

        $this->assertTrue($invoice->relationLoaded('customer'));
        $this->assertTrue($invoice->relationLoaded('orders'));
        $this->assertFalse($invoice->relationLoaded('products'));
    }

    public function test_find_works_with_filters_built_from_the_filter_array(): void
    {
        $customer = $this->createCustomer();
        $match = $this->createInvoice(customer: $customer, dueDate: '2026-03-10');
        $this->createInvoice(customer: $customer, dueDate: '2026-05-10');
        $this->createInvoice(dueDate: '2026-03-10');

        $query = InvoiceFilters::from([
            'customer_id' => $customer->customer_id,
            'invoice_from' => '2026-03-01',
            'invoice_to' => '2026-03-31',
        ]);

        $found = $this->repository->find($query);

        $this->assertCount(1, $found);
        $this->assertSame($match->invoice_id, $found->first()->invoice_id);
    }

    // ====
    // paginate
    // ====

    public function test_paginate_without_a_query_returns_all_invoices_unfiltered(): void
    {
        $this->createInvoice();
        $this->createInvoice();

        $page = $this->repository->paginate();

        $this->assertSame(2, $page->total());
        $this->assertCount(2, $page->items());
    }

    public function test_paginate_respects_per_page_and_reports_the_total(): void
    {
        for ($n = 0; $n < 5; $n++) {
            $this->createInvoice();
        }

        $page = $this->repository->paginate(2, new InvoiceQuery());

        $this->assertSame(5, $page->total());
        $this->assertCount(2, $page->items());
        $this->assertSame(3, $page->lastPage());
    }

    public function test_paginate_sorts_by_customer_name(): void
    {
        $this->createInvoice(customer: $this->createCustomer('Charlie'));
        $this->createInvoice(customer: $this->createCustomer('Alpha'));
        $this->createInvoice(customer: $this->createCustomer('Bravo'));

        $ascQuery = (new InvoiceQuery())->sortBy('customer', SortOrder::ASC);
        $ascQuery->relationships(['customer']);
        $descQuery = (new InvoiceQuery())->sortBy('customer', SortOrder::DESC);
        $descQuery->relationships(['customer']);

        $asc = $this->repository->paginate(10, $ascQuery);
        $desc = $this->repository->paginate(10, $descQuery);

        $this->assertSame(['Alpha', 'Bravo', 'Charlie'], collect($asc->items())->map(fn($i) => $i->customer->customer_name)->all());
        $this->assertSame(['Charlie', 'Bravo', 'Alpha'], collect($desc->items())->map(fn($i) => $i->customer->customer_name)->all());
    }

    public function test_paginate_sorted_by_customer_keeps_the_invoice_own_columns(): void
    {
        $invoice = $this->createInvoice(total: 321.00);
        // make the customer's timestamp differ from the invoice's, otherwise same-second inserts hide the overwrite
        DB::table('customers')->update(['created_at' => '2001-01-01 00:00:00']);

        $row =$this->repository->paginate(10, (new InvoiceQuery())->sortBy('customer', SortOrder::ASC))->first();

        $this->assertSame($invoice->invoice_id, $row->invoice_id);
        $this->assertSame($invoice->invoice_number, $row->invoice_number);
        $this->assertEquals(321.00, $row->invoice_total);
        // the customers table has the same timestamp columns, they must not overwrite the invoice's
        $this->assertEquals($invoice->fresh()->created_at, $row->created_at);
    }

    public function test_paginate_with_customer_filter_and_customer_sort_together(): void
    {
        $customer = $this->createCustomer('Alpha');
        $mine = $this->createInvoice(customer: $customer, dueDate: '2026-03-10');
        $this->createInvoice(dueDate: '2026-03-10');

        $query = (new InvoiceQuery())
            ->customerId($customer->customer_id)
            ->invoiceDueFrom('2026-03-01')
            ->invoiceDueTo('2026-03-31')
            ->sortBy('customer', SortOrder::ASC);

        $page = $this->repository->paginate(10, $query);

        $this->assertSame(1, $page->total());
        $this->assertSame($mine->invoice_id, $page->first()->invoice_id);
    }

    // ====
    // sorting
    // ====

    /**
     * Three invoices whose values differ on every sortable column, created in a different order
     * than any of the sorted orders (except invoice_number, which follows creation order A, B, C).
     *
     * @return array<string, int> label => invoice_id
     */
    private function createSortFixture(): array
    {
        $a = $this->createInvoice(InvoiceStatus::UNPAID, 30.00, $this->createCustomer('Bravo'), '2026-03-10', '2026-01-03');
        $b = $this->createInvoice(InvoiceStatus::PAID, 10.00, $this->createCustomer('Charlie'), '2026-05-10', '2026-01-01');
        $c = $this->createInvoice(InvoiceStatus::CANCELLED, 20.00, $this->createCustomer('Alpha'), '2026-04-10', '2026-01-02');

        return ['A' => $a->invoice_id, 'B' => $b->invoice_id, 'C' => $c->invoice_id];
    }

    /**
     * @return int[]
     */
    private function sortedIds(InvoiceQuery $query, int $perPage = 100): array
    {
        return collect($this->repository->paginate($perPage, $query)->items())->pluck('invoice_id')->all();
    }

    /**
     * Each sortable column of the invoice list with the fixture labels in ascending order
     *
     * @return array<string, array{string, string[]}>
     */
    public static function sortableColumns(): array
    {
        return [
            'invoice_number' => ['invoice_number', ['A', 'B', 'C']],
            'customer' => ['customer', ['C', 'A', 'B']],
            'invoice_total' => ['invoice_total', ['B', 'C', 'A']],
            'invoice_due_date' => ['invoice_due_date', ['A', 'C', 'B']],
            'invoice_issue_date' => ['invoice_issue_date', ['B', 'C', 'A']],
            'invoice_status_new' => ['invoice_status_new', ['B', 'A', 'C']],
        ];
    }

    /**
     * @param string[] $ascending
     */
    #[DataProvider('sortableColumns')]
    public function test_paginate_orders_by_column_in_both_directions(string $field, array $ascending): void
    {
        $ids = $this->createSortFixture();
        $expected = array_map(fn(string $label) => $ids[$label], $ascending);

        $asc = $this->sortedIds((new InvoiceQuery())->sortBy($field, SortOrder::ASC));
        $desc = $this->sortedIds((new InvoiceQuery())->sortBy($field, SortOrder::DESC));

        $this->assertSame($expected, $asc, "$field ascending");
        $this->assertSame(array_reverse($expected), $desc, "$field descending");
    }

    public function test_sort_direction_defaults_to_descending(): void
    {
        $ids = $this->createSortFixture();

        $sorted = $this->sortedIds((new InvoiceQuery())->sortBy('invoice_total'));

        $this->assertSame([$ids['A'], $ids['C'], $ids['B']], $sorted);
    }

    public function test_sorting_by_customer_does_not_change_which_invoices_are_returned(): void
    {
        $this->createSortFixture();
        $this->createInvoice(customer: $this->createCustomer('Delta'));

        $unsorted = $this->sortedIds(new InvoiceQuery());
        $sorted = $this->sortedIds((new InvoiceQuery())->sortBy('customer', SortOrder::ASC));

        $this->assertCount(4, $sorted);
        $this->assertEqualsCanonicalizing($unsorted, $sorted);
        $this->assertSame(4, $this->repository->paginate(2, (new InvoiceQuery())->sortBy('customer'))->total());
    }

    public function test_sorting_is_applied_together_with_filters(): void
    {
        $customer = $this->createCustomer();
        $low = $this->createInvoice(total: 10.00, customer: $customer, dueDate: '2026-03-05');
        $high = $this->createInvoice(total: 90.00, customer: $customer, dueDate: '2026-03-20');
        $mid = $this->createInvoice(total: 50.00, customer: $customer, dueDate: '2026-03-12');
        $this->createInvoice(total: 70.00, customer: $customer, dueDate: '2026-06-01');
        $this->createInvoice(total: 80.00, dueDate: '2026-03-10');

        $query = (new InvoiceQuery())
            ->customerId($customer->customer_id)
            ->invoiceDueFrom('2026-03-01')
            ->invoiceDueTo('2026-03-31')
            ->sortBy('invoice_total', SortOrder::DESC);

        $this->assertSame([$high->invoice_id, $mid->invoice_id, $low->invoice_id], $this->sortedIds($query));
    }

    /**
     * Sort fields where every row has the same value, so only the tie-breaker decides the order
     *
     * @return array<string, array{string, SortOrder}>
     */
    public static function tiedSorts(): array
    {
        $sets = [];
        foreach (['customer', 'invoice_total', 'invoice_due_date', 'invoice_issue_date', 'invoice_status_new'] as $field) {
            foreach ([SortOrder::ASC, SortOrder::DESC] as $order) {
                $sets["$field {$order->value}"] = [$field, $order];
            }
        }
        return $sets;
    }

    #[DataProvider('tiedSorts')]
    public function test_ties_are_ordered_by_newest_invoice_first_and_paginate_consistently(string $field, SortOrder $order): void
    {
        // 7 invoices on 2 customers, identical on every column except customer and number
        $alpha = $this->createCustomer('Alpha');
        $bravo = $this->createCustomer('Bravo');
        $customers = [];
        foreach (range(1, 7) as $n) {
            $customer = $n % 2 ? $alpha : $bravo;
            $invoice = $this->createInvoice(InvoiceStatus::UNPAID, 10.00, $customer, '2026-03-10', '2026-03-01');
            $customers[$invoice->invoice_id] = $customer->customer_name;
        }

        $query = fn() => (new InvoiceQuery())->sortBy($field, $order);

        // expected: by the sorted value (customer name only for the customer sort), then newest invoice first
        $expected = array_keys($customers);
        usort($expected, function (int $x, int $y) use ($field, $order, $customers) {
            if ($field === 'customer' && $customers[$x] !== $customers[$y]) {
                $byName = strcmp($customers[$x], $customers[$y]);
                return $order === SortOrder::ASC ? $byName : -$byName;
            }
            return $y <=> $x;
        });

        $this->assertSame($expected, $this->sortedIds($query()), "$field {$order->value} full list");

        // paging through 4 pages of 2 returns the same sequence, nothing repeated or skipped
        $paged = [];
        foreach ([1, 2, 3, 4] as $page) {
            $this->app['request']->merge(['page' => $page]);
            $paged = array_merge($paged, $this->sortedIds($query(), 2));
        }

        $this->assertSame($expected, $paged, "$field {$order->value} paged");
        $this->assertCount(7, array_unique($paged));
    }

    // ====
    // getInvoiceTotals
    // ====

    public function test_get_invoice_totals_sums_all_invoices_without_a_query(): void
    {
        $this->createInvoice(InvoiceStatus::UNPAID, 100.25);
        $this->createInvoice(InvoiceStatus::PAID, 50.50);
        $this->createInvoice(InvoiceStatus::CANCELLED, 10.00);

        $this->assertEqualsWithDelta(160.75, $this->repository->getInvoiceTotals(), 0.001);
    }

    public function test_get_invoice_totals_respects_customer_and_due_date_filters(): void
    {
        $customer = $this->createCustomer();
        $this->createInvoice(total: 100.00, customer: $customer, dueDate: '2026-03-05');
        $this->createInvoice(total: 40.00, customer: $customer, dueDate: '2026-03-20');
        $this->createInvoice(total: 999.00, customer: $customer, dueDate: '2026-05-01');
        $this->createInvoice(total: 500.00, dueDate: '2026-03-10');

        $query = (new InvoiceQuery())
            ->customerId($customer->customer_id)
            ->invoiceDueFrom('2026-03-01')
            ->invoiceDueTo('2026-03-31');

        $this->assertEqualsWithDelta(140.00, $this->repository->getInvoiceTotals($query), 0.001);
    }

    public function test_get_invoice_totals_returns_zero_float_when_nothing_matches(): void
    {
        $this->createInvoice(total: 100.00, dueDate: '2026-03-10');

        $total = $this->repository->getInvoiceTotals((new InvoiceQuery())->invoiceDueFrom('2030-01-01'));

        $this->assertIsFloat($total);
        $this->assertSame(0.0, $total);
    }

    public function test_get_invoice_totals_does_not_load_relationships_or_fail_on_default_columns(): void
    {
        $this->createInvoice(total: 20.00);

        // regression: the default select of every column used to be mixed into the aggregate query
        $total = $this->repository->getInvoiceTotals(InvoiceFilters::from([]));

        $this->assertEqualsWithDelta(20.00, $total, 0.001);
    }

    // ====
    // getInvoiceCount
    // ====

    public function test_get_invoice_count_counts_all_invoices_without_a_query(): void
    {
        $this->createInvoice();
        $this->createInvoice(InvoiceStatus::PAID);

        $this->assertSame(2, $this->repository->getInvoiceCount());
    }

    public function test_get_invoice_count_respects_filters(): void
    {
        $customer = $this->createCustomer();
        $this->createInvoice(customer: $customer, dueDate: '2026-03-05');
        $this->createInvoice(customer: $customer, dueDate: '2026-03-20');
        $this->createInvoice(customer: $customer, dueDate: '2026-05-01');
        $this->createInvoice(dueDate: '2026-03-10');

        $query = InvoiceFilters::from([
            'customer_id' => $customer->customer_id,
            'invoice_from' => '2026-03-01',
            'invoice_to' => '2026-03-31',
        ]);

        $this->assertSame(2, $this->repository->getInvoiceCount($query));
    }

    public function test_get_invoice_count_returns_zero_when_there_are_no_invoices(): void
    {
        $this->assertSame(0, $this->repository->getInvoiceCount());
    }

    public function test_get_invoice_count_matches_paginate_total_for_the_same_query(): void
    {
        $customer = $this->createCustomer();
        $this->createInvoice(customer: $customer, dueDate: '2026-03-05');
        $this->createInvoice(customer: $customer, dueDate: '2026-03-20');
        $this->createInvoice(dueDate: '2026-03-10');

        $query = (new InvoiceQuery())->customerId($customer->customer_id);

        $this->assertSame(
            $this->repository->getInvoiceCount($query),
            $this->repository->paginate(10, $query)->total()
        );
    }
}
