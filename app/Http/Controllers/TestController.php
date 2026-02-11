<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function testLivewireOrderList()
    {
        $testData = [];

        return view('orders.test.test-order-list', [
            'testData' => $testData
        ]);
    }
}
