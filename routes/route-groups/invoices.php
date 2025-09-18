<?php
use Illuminate\Support\Facades\Route;

// invoice list
Route::get('/', 'index')->name('invoices.index');

// download an invoice
Route::get('/download/{invoice}', 'download')->name('invoices.download');

// view a pdf in browser
Route::get('/view-inline/{invoice}', 'viewInline')->name('invoices.view-inline');

// create manual invoice form
Route::get('/create', 'createManual')->name('invoices.create');

// create single
Route::get('/create-single/{order}', 'createSingleInvoice')->name('invoices.create-single');
