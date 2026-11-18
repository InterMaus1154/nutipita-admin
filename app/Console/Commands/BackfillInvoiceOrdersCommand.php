<?php

namespace App\Console\Commands;

use App\Domain\Invoice\Entities\Invoice;
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

        // invoices with an order_id belong to that single order, the legacy invoices.order_id is the exact link.
        // They are handled here only and excluded from the date range match below
        $rowCount = DB::table('invoice_orders')->insertOrIgnoreUsing(
            ['invoice_id', 'order_id'],
            DB::table('invoices')->whereNotNull('order_id')->select('invoice_id', 'order_id')
        );
        $singleOrderRows = $rowCount;

        Invoice::query()
            ->whereNull('order_id')
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

        $message = "Backfilled invoice_orders: {$singleOrderRows} rows from invoices.order_id, "
            . ($rowCount - $singleOrderRows) . " rows from date ranges for {$invoiceCount} invoices";
        $this->info($message);
        Log::info($message);

        return self::SUCCESS;
    }
}
