<?php

namespace App\Livewire\Order;

use App\Domain\Invoice\Exceptions\CannotCreateInvoiceException;
use App\Domain\Invoice\Services\InvoiceService;
use App\Domain\Invoice\Strategies\CreateInvoiceStrategyFactory;
use App\Livewire\OrderList;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderPopupCard extends Component
{
    public ?int $orderId = null;

    private OrderService $orderService;

    private InvoiceService $invoiceService;
    private CreateInvoiceStrategyFactory $strategies;

    public function boot(OrderService $orderService, InvoiceService $invoiceService, CreateInvoiceStrategyFactory $createInvoiceStrategyFactory): void
    {
        $this->orderService = $orderService;
        $this->invoiceService = $invoiceService;
        $this->strategies = $createInvoiceStrategyFactory;

    }

    public function createInvoice(): void
    {
        if (is_null($this->orderId)) {
            return;
        }

        try {
            $this->invoiceService->createRecordUsingStrategy($this->strategies->single($this->orderId));
            session()->flash('success', 'Invoice created');
        } catch (CannotCreateInvoiceException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Throwable $e) {
            session()->flash('error', 'Unknown error when generating invoice');
            Log::error($e->getMessage(), ['exception' => $e]);
        }

        $this->dispatch('refresh')->to(OrderList::class);
    }

    public function deleteOrder(): void
    {
        if (is_null($this->orderId)) {
            return;
        }

        $this->orderService->deleteOrder($this->orderId);
        $this->orderId = null;
        $this->dispatch('modal-clear');
        $this->dispatch('refresh');
    }

    public function editOrder(): void
    {
        if (is_null($this->orderId)) {
            return;
        }

        $this->dispatch('modal-open', 'modal.order-edit', ['order_id' => $this->orderId]);
    }

    #[On('refresh')]
    public function refresh(): void
    {

    }

    public function render(): View
    {
        $order = Order::query()
            ->where('order_id', $this->orderId)
            ->with('products', 'customer', 'invoice', 'invoices')
            ->first();

        return view('livewire.order.order-popup-card', compact('order'));
    }
}
