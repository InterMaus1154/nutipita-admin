<?php

namespace App\Console\Commands;

use App\Domain\Invoice\Invoice;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillInvoiceOrdersCommand extends Command
{
    protected $signature = 'app:backfill-invoice-orders-command';

    protected $description = 'Backfill the invoice_orders table with invoices and orders belonging to them';


    public function handle(): int
    {
        $invoiceCount = 0;
        $rowCount = 0;

        Invoice::query()
            ->whereNotNull('invoice_from')
            ->whereNotNull('invoice_to')
            ->whereDoesntHave('orders')
            ->chunkById(50, function (Collection $invoices) use (&$invoiceCount, &$rowCount) {
                foreach ($invoices as $invoice) {
                    $rows = Order::query()->forInvoice($invoice)->get()
                        ->map(fn(Order $order) => [
                            'invoice_id' => $invoice->getId(),
                            'order_id' => $order->getId(),
                        ])->all();

                    $rowCount += DB::table('invoice_orders')->insertOrIgnore($rows);
                    $invoiceCount++;
                }
            }, 'invoice_id');

        $message = "Backfilled invoice_orders: {$rowCount} rows inserted for {$invoiceCount} invoices";
        $this->info($message);
        Log::info($message);

        return self::SUCCESS;
    }
}
