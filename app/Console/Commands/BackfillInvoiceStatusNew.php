<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillInvoiceStatusNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:backfill-invoice-status-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill invoice_status_new (int) from the legacy invoice_status (enum) column';

    private const array MAP = [
        'paid' => 0,
        'due' => 1,
        'cancelled' => 2,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            DB::transaction(function () {
                $total = DB::table('invoices')->whereNull('invoice_status_new')->count();

                if ($total === 0) {
                    $this->info('No invoices need backfilling.');
                    return;
                }

                $this->info("Backfilling {$total} invoice(s)...");

                foreach (self::MAP as $status => $value) {
                    $updated = DB::table('invoices')
                        ->where('invoice_status', $status)
                        ->whereNull('invoice_status_new')
                        ->update(['invoice_status_new' => $value]);

                    $this->line("  {$status} -> {$value}: {$updated} row(s) updated");
                }

                $discrepancies = $this->countDiscrepancies();

                if ($discrepancies > 0) {
                    throw new \RuntimeException("Found {$discrepancies} discrepant row(s) after backfill.");
                }

                $this->info('Backfill verified. Committing.');
            });
        } catch (\Throwable $e) {
            $this->error('Backfill failed and was rolled back: ' . $e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Count rows where invoice_status_new doesn't match what invoice_status maps to,
     * including rows left unmapped (null) despite having a status.
     */
    private function countDiscrepancies(): int
    {
        return DB::table('invoices')
            ->whereNotNull('invoice_status')
            ->where(function ($query) {
                $query->whereNull('invoice_status_new');

                foreach (self::MAP as $status => $value) {
                    $query->orWhere(function ($q) use ($status, $value) {
                        $q->where('invoice_status', $status)
                            ->where('invoice_status_new', '!=', $value);
                    });
                }
            })
            ->count();
    }
}
