<?php

namespace NutiPita\Http\Controllers;

use NutiPita\Models\Order;
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
