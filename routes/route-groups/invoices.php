<?php
use Illuminate\Support\Facades\Route;

// invoice list
Route::get('/', 'index')->name('invoices.index');

// download an invoice
Route::get('/download/{invoiceId}', 'download')->name('invoices.download');

// view a pdf in browser
Route::get('/view-inline/{invoice}', 'viewInline')->name('invoices.view-inline');

// create manual invoice form
Route::get('/create', 'createManual')->name('invoices.create');

Route::get('/create-invoice-summary/{id}', 'createInvoiceSummary')->name('invoices.create-summary');
