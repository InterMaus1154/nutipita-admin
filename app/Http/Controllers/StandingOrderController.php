<?php

namespace App\Http\Controllers;

use App\Http\Requests\StandingOrderRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StandingOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StandingOrderController extends Controller
{
    /*
     * Index listing page
     */
    public function index(): View
    {
        $orders = StandingOrder::with('customer')->select(['standing_order_id', 'customer_id', 'is_active', 'start_from'])->get();
        return view('standing_orders.index', compact('orders'));
    }

    /*
     * Show create form
     */
    public function create(): View
    {
        $customers = Customer::select(['customer_id', 'customer_name'])->get();
        $products = Product::select(['product_id', 'product_name'])->get();
        return view('standing_orders.create', compact('customers', 'products'));
    }

    /*
     * Store new standing order
     */
    public function store(StandingOrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $startDate = $request->date('start_from');
            // only make it active, if the start date is from today, or was (already active) before today
            $isActive = false;
            if ($startDate->isToday() || $startDate->isPast()) {
                $isActive = true;
            }
            $order = StandingOrder::create([
                'customer_id' => $request->validated('customer_id'),
                'start_from' => $startDate->toDateString(),
                'is_active' => $isActive
            ]);

            // collect days that has products with more than 0 quantity
            $dayNums = [];
            foreach ($request->array('products') as $dayNum => $products) {
                foreach ($products as $quantity) {
                    if ((int)$quantity > 0) {
                        $dayNums[] = $dayNum;
                        break;
                    }
                }
            }

            $daysData = [];
            foreach ($dayNums as $day) {
                // prepare data for bulk database insert
                $daysData[] = ['day' => $day];
            }

            // bulk insert days for the order
            // only inserted that have more than 0 products
            $days = $order->days()->createMany($daysData)->keyBy('day');
            //
            foreach ($request->array('products') as $dayNum => $products) {
                // check if that day is available
                // not available, if previously excluded - having 0 products
                if (!isset($days[$dayNum])) {
                    continue;
                }
                $day = $days[$dayNum];
                $productData = [];
                foreach ($products as $product_id => $qty) {
                    // product with more than 0 quantity only
                    if ((int)$qty > 0) {
                        // prepare data for bulk insert
                        $productData[] = [
                            'product_id' => $product_id,
                            'product_qty' => $qty
                        ];
                    }
                }
                // if non-empty, bulk insert the products for the specific day
                if (!empty($productData)) {
                    $day->products()->createMany($productData);
                }
            }

            DB::commit();
            return redirect()
                ->route('standing-orders.index')
                ->with('success', 'Standing order successfully created');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect()
                ->route('standing-orders.create')
                ->withErrors(['create_error' => 'Error at creating standing order', 'e' => $e->getMessage()]);
        }
    }

    /*
     * Show order detail page
     */
    public function show(StandingOrder $order): View
    {
        $products = Product::select(['product_id', 'product_name'])->orderBy('product_id')->get();
        $order->loadMissing('customer:customer_id,customer_name', 'days', 'days.products');
        return view('standing_orders.show', compact('order', 'products'));
    }

    /*
     * Show edit page
     */
    public function edit(StandingOrder $order): View
    {
        $products = Product::select(['product_id', 'product_name'])->get();
        $order->loadMissing('days', 'days.products', 'customer');
        return view('standing_orders.edit', compact('order', 'products'));
    }
}
