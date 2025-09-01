<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;


class CreditNoteController extends Controller
{
    public function test()
    {
        $invoice = Invoice::firstWhere('invoice_number', '0025');
        $creditNote = new CreditNote();
        $creditNote->invoice_id = $invoice->invoice_id;
        $creditNote->credit_note_number = 'CN-'.$invoice->invoice_number;
        $creditNote->credit_note_issued_at = "2025-08-20";

        $customer = $invoice->customer;
        $products = $invoice->products;

        return Pdf::loadView('pdf.credit-note', [
            'customer' => $customer,
            'creditNote' => $creditNote,
            'invoice' => $invoice,
            'products' => $products
        ])
            ->setPaper('A4', 'portrait')
            ->stream($creditNote->credit_note_number.'.pdf');
    }
}
