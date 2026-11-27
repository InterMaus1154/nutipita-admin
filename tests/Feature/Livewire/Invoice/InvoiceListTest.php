<?php

namespace Tests\Feature\Livewire\Invoice;

use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Livewire\Invoice\InvoiceList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\TestHelpers;

class InvoiceListTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    protected function setUp(): void
    {
        parent::setUp();

        // .env.testing has no APP_KEY and rendering the views needs the encrypter
        config(['app.key' => 'base64:' . base64_encode(random_bytes(32))]);

        Storage::fake('local');
        Storage::fake('invoices');
    }

    private function storedInvoiceStatus(int $invoiceId): int
    {
        return (int)DB::table('invoices')->where('invoice_id', $invoiceId)->value('invoice_status_new');
    }

    // ====
    // rendering
    // ====

    public function test_it_lists_invoices_with_customer_names(): void
    {
        $first = $this->createInvoice(customer: $this->createCustomer('Alpha Bakery'));
        $second = $this->createInvoice(customer: $this->createCustomer('Beta Cafe'));

        Livewire::test(InvoiceList::class)
            ->assertSee('INV-' . $first->invoice_number)
            ->assertSee('INV-' . $second->invoice_number)
            ->assertSee('Alpha Bakery')
            ->assertSee('Beta Cafe');
    }

    public function test_it_defaults_to_sorting_by_invoice_number_descending(): void
    {
        Livewire::test(InvoiceList::class)
            ->assertSet('sortField', 'invoice_number')
            ->assertSet('sortDirection', 'desc');
    }

    // ====
    // filters
    // ====

    public function test_update_filter_event_filters_by_customer(): void
    {
        $shown = $this->createInvoice(customer: $this->createCustomer('Shown Customer'));
        $hidden = $this->createInvoice(customer: $this->createCustomer('Hidden Customer'));

        Livewire::test(InvoiceList::class)
            ->dispatch('update-filter', filters: ['customer_id' => $shown->customer_id])
            ->assertSet('filters.customer_id', $shown->customer_id)
            ->assertSee('Shown Customer')
            ->assertDontSee('Hidden Customer');
    }

    public function test_update_filter_event_keeps_filters_it_was_not_given(): void
    {
        $customer = $this->createCustomer();

        Livewire::test(InvoiceList::class)
            ->dispatch('update-filter', filters: ['customer_id' => $customer->customer_id])
            ->dispatch('update-filter', filters: ['invoice_from' => '2026-01-01'])
            ->assertSet('filters.customer_id', $customer->customer_id)
            ->assertSet('filters.invoice_from', '2026-01-01');
    }

    // ====
    // sorting
    // ====

    public function test_set_sort_changes_the_field_and_toggles_the_direction(): void
    {
        Livewire::test(InvoiceList::class)
            ->call('setSort', 'invoice_total')
            ->assertSet('sortField', 'invoice_total')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_mobile_sort_event_sets_field_and_direction(): void
    {
        Livewire::test(InvoiceList::class)
            ->dispatch('set-mobile-sort', data: ['value' => 'asc:invoice_status_new'])
            ->assertSet('sortField', 'invoice_status_new')
            ->assertSet('sortDirection', 'asc');
    }

    public function test_sorting_by_customer_still_renders(): void
    {
        $this->createInvoice(customer: $this->createCustomer('Zed Ltd'));
        $this->createInvoice(customer: $this->createCustomer('Abe Ltd'));

        Livewire::test(InvoiceList::class)
            ->call('setSort', 'customer')
            ->assertOk()
            ->assertSeeInOrder(['Abe Ltd', 'Zed Ltd']);
    }

    // ====
    // updateInvoiceStatus
    // ====

    public function test_update_invoice_status_updates_the_invoice_and_its_orders(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);
        $order = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach($order->order_id);

        Livewire::test(InvoiceList::class)
            ->call('updateInvoiceStatus', InvoiceStatus::PAID->value, $invoice->invoice_id)
            ->assertSee('Invoice updated');

        $this->assertSame(InvoiceStatus::PAID->value, $this->storedInvoiceStatus($invoice->invoice_id));
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    public function test_update_invoice_status_can_mark_a_paid_invoice_unpaid_again(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::PAID);
        $order = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $invoice->orders()->attach($order->order_id);

        Livewire::test(InvoiceList::class)
            ->call('updateInvoiceStatus', InvoiceStatus::UNPAID->value, $invoice->invoice_id);

        $this->assertSame(InvoiceStatus::UNPAID->value, $this->storedInvoiceStatus($invoice->invoice_id));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($order));
    }

    public function test_update_invoice_status_with_an_invalid_status_shows_an_error_and_changes_nothing(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);
        $order = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach($order->order_id);

        Livewire::test(InvoiceList::class)
            ->call('updateInvoiceStatus', 99, $invoice->invoice_id)
            ->assertSee('Invalid status value')
            ->assertDontSee('Invoice updated');

        $this->assertSame(InvoiceStatus::UNPAID->value, $this->storedInvoiceStatus($invoice->invoice_id));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($order));
    }

    public function test_update_invoice_status_for_an_unknown_invoice_shows_an_error(): void
    {
        Livewire::test(InvoiceList::class)
            ->call('updateInvoiceStatus', InvoiceStatus::PAID->value, 999999)
            ->assertSee('999999')
            ->assertDontSee('Invoice updated');
    }

    // ====
    // delete
    // ====

    public function test_delete_removes_the_invoice_and_releases_its_orders(): void
    {
        $invoice = $this->createInvoice();
        $order = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach($order->order_id);

        Livewire::test(InvoiceList::class)
            ->call('delete', $invoice->invoice_id)
            ->assertSee('Invoice successfully deleted');

        $this->assertDatabaseMissing('invoices', ['invoice_id' => $invoice->invoice_id]);
        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($order));
    }

    public function test_delete_of_an_unknown_invoice_shows_an_error(): void
    {
        $invoice = $this->createInvoice();

        Livewire::test(InvoiceList::class)
            ->call('delete', 999999)
            ->assertSee('Error at deleting invoice')
            ->assertDontSee('Invoice successfully deleted');

        $this->assertDatabaseHas('invoices', ['invoice_id' => $invoice->invoice_id]);
    }

    // ====
    // refresh
    // ====

    public function test_refresh_event_rerenders_with_new_data(): void
    {
        $component = Livewire::test(InvoiceList::class);
        $invoice = $this->createInvoice(customer: $this->createCustomer('Late Arrival'));

        $component->dispatch('refresh')
            ->assertSee('Late Arrival');
    }

    // ====
    // mobile view
    // ====

    private function mobileList(): \Livewire\Features\SupportTesting\Testable
    {
        return Livewire::test(InvoiceList::class)->set('isMobile', true);
    }

    public function test_it_detects_a_phone_user_agent(): void
    {
        $previous = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 '
            . '(KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';

        try {
            Livewire::test(InvoiceList::class)->assertSet('isMobile', true);
        } finally {
            $previous === null ? $_SERVER['HTTP_USER_AGENT'] = '' : $_SERVER['HTTP_USER_AGENT'] = $previous;
        }
    }

    public function test_it_is_not_mobile_for_a_desktop_user_agent(): void
    {
        $previous = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36';

        try {
            Livewire::test(InvoiceList::class)->assertSet('isMobile', false);
        } finally {
            $previous === null ? $_SERVER['HTTP_USER_AGENT'] = '' : $_SERVER['HTTP_USER_AGENT'] = $previous;
        }
    }

    public function test_mobile_view_renders_a_card_per_invoice(): void
    {
        $first = $this->createInvoice(customer: $this->createCustomer('Alpha Bakery'), total: 123.45);
        $second = $this->createInvoice(customer: $this->createCustomer('Beta Cafe'));

        $this->mobileList()
            ->assertSee('INV-' . $first->invoice_number)
            ->assertSee('INV-' . $second->invoice_number)
            ->assertSee('Alpha Bakery')
            ->assertSee('Beta Cafe')
            ->assertSee('123.45')
            ->assertSeeHtml('wire:key="invoice-mobile-card-' . $first->invoice_id . '"')
            ->assertSeeHtml('wire:key="invoice-mobile-card-' . $second->invoice_id . '"');
    }

    public function test_mobile_card_offers_mark_paid_for_an_unpaid_invoice(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);

        $this->mobileList()
            ->assertSee('Mark Paid')
            ->assertDontSee('Mark Unpaid')
            ->assertSeeHtml('wire:click="updateInvoiceStatus(' . InvoiceStatus::PAID->value . ', ' . $invoice->invoice_id . ')"');
    }

    public function test_mobile_card_offers_mark_unpaid_for_a_paid_invoice(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::PAID);

        // exact string so a malformed expression in the attribute can't slip through unnoticed
        $this->mobileList()
            ->assertSee('Mark Unpaid')
            ->assertDontSee('Mark Paid')
            ->assertSeeHtml('wire:click="updateInvoiceStatus(' . InvoiceStatus::UNPAID->value . ', ' . $invoice->invoice_id . ')"');
    }

    public function test_mobile_card_offers_mark_unpaid_for_loss_and_cancelled_invoices(): void
    {
        $this->createInvoice(InvoiceStatus::LOSS);
        $this->createInvoice(InvoiceStatus::CANCELLED);

        $this->mobileList()
            ->assertSee('Mark Unpaid')
            ->assertDontSee('Mark Paid');
    }

    public function test_mobile_card_has_the_delete_and_popup_actions(): void
    {
        $invoice = $this->createInvoice(customer: $this->createCustomer('Alpha Bakery'));

        $this->mobileList()
            ->assertSeeHtml('wire:click="delete(' . $invoice->invoice_id . ')"')
            ->assertSee('Delete Invoice')
            ->assertSeeHtml('invoice.invoice-popup-card')
            ->assertSeeHtml('invoiceId: ' . $invoice->invoice_id);
    }

    public function test_mobile_card_has_the_pdf_and_summary_links(): void
    {
        $invoice = $this->createInvoice();

        $this->mobileList()
            ->assertSeeHtml(route('invoices.download', ['invoiceId' => $invoice->invoice_id]))
            ->assertSeeHtml(route('invoices.create-summary', ['id' => $invoice->invoice_id]))
            ->assertSee('View PDF');
    }

    public function test_mobile_status_select_is_rendered_for_each_card(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::PAID);

        $this->mobileList()
            ->assertSeeHtml('invoice-status-' . $invoice->invoice_id . '-' . InvoiceStatus::PAID->value)
            ->assertSeeHtml('updateInvoiceStatus');
    }

    public function test_mobile_mark_paid_action_updates_the_invoice_and_its_orders(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);
        $order = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach($order->order_id);

        // the exact call the Mark Paid link makes
        $this->mobileList()
            ->call('updateInvoiceStatus', InvoiceStatus::PAID->value, $invoice->invoice_id)
            ->assertSee('Invoice updated')
            ->assertSee('Mark Unpaid')
            ->assertDontSee('Mark Paid');

        $this->assertSame(InvoiceStatus::PAID->value, $this->storedInvoiceStatus($invoice->invoice_id));
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    public function test_mobile_mark_unpaid_action_updates_the_invoice_and_its_orders(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::PAID);
        $order = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $invoice->orders()->attach($order->order_id);

        $this->mobileList()
            ->call('updateInvoiceStatus', InvoiceStatus::UNPAID->value, $invoice->invoice_id)
            ->assertSee('Invoice updated')
            ->assertSee('Mark Paid');

        $this->assertSame(InvoiceStatus::UNPAID->value, $this->storedInvoiceStatus($invoice->invoice_id));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($order));
    }

    public function test_mobile_delete_action_removes_the_card(): void
    {
        $invoice = $this->createInvoice(customer: $this->createCustomer('Gone Bakery'));

        $this->mobileList()
            ->assertSee('INV-' . $invoice->invoice_number)
            ->call('delete', $invoice->invoice_id)
            ->assertSee('Invoice successfully deleted')
            ->assertDontSee('INV-' . $invoice->invoice_number)
            ->assertDontSee('Gone Bakery');
    }

    public function test_mobile_filter_shows_only_the_filtered_customers_cards(): void
    {
        $shown = $this->createInvoice(customer: $this->createCustomer('Shown Customer'));
        $this->createInvoice(customer: $this->createCustomer('Hidden Customer'));

        $this->mobileList()
            ->dispatch('update-filter', filters: ['customer_id' => $shown->customer_id])
            ->assertSee('Shown Customer')
            ->assertDontSee('Hidden Customer');
    }

    public function test_mobile_sort_event_reorders_the_cards(): void
    {
        $this->createInvoice(customer: $this->createCustomer('Zed Ltd'));
        $this->createInvoice(customer: $this->createCustomer('Abe Ltd'));

        $this->mobileList()
            ->dispatch('set-mobile-sort', data: ['value' => 'asc:customer'])
            ->assertSeeInOrder(['Abe Ltd', 'Zed Ltd'])
            ->dispatch('set-mobile-sort', data: ['value' => 'desc:customer'])
            ->assertSeeInOrder(['Zed Ltd', 'Abe Ltd']);
    }
}
