<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // show customer list
    public function index()
    {
        $customers = Customer::select('customer_id', 'customer_name', 'customer_phone')->withCount('orders')->get();
        return view('customers.index', compact('customers'));
    }
}
